<?php

namespace App\Services;

use App\Models\Outcome;
use App\Models\Subject;
use App\Services\AI\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OpenAIService implements AiProviderInterface
{
    private string $apiKey;
    private string $model = 'gpt-4o-mini';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Analyze an uploaded question image using GPT-4o Vision.
     * Returns subject name, outcome name, and OCR text.
     */
    public function analyzeImage(string $imagePath): array
    {
        // $imagePath is already an absolute path from the controller
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $subjects = Subject::with('outcomes')->get();
        $subjectList = $subjects->map(function ($s) {
            $outcomes = $s->outcomes->pluck('name')->join(', ');
            return "{$s->name}: [{$outcomes}]";
        })->join("\n");

        $systemPrompt = "Sen bir eğitim soru analiz asistanısın. Türkiye'deki 5-8. sınıf müfredatına göre soruları analiz edersin.

Sistemde aşağıdaki dersler ve kazanımlar mevcut:
{$subjectList}

Verilen soru fotoğrafını analiz et ve şu bilgileri JSON olarak döndür:
1. \"subject\": Sorunun ait olduğu ders adı (yukarıdaki listeden)
2. \"outcome\": Sorunun ait olduğu kazanım adı (yukarıdaki listeden)
3. \"ocr_text\": Sorudaki metin (OCR)
4. \"explanation\": Kısa analiz açıklaması

Sadece JSON döndür, başka bir şey yazma.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$imageData}",
                                    ],
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'Bu soruyu analiz et.',
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 1000,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallbackAnalysis();
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $data = json_decode(trim($content), true);

            if (!$data) {
                Log::warning('OpenAI response not valid JSON', ['content' => $content]);
                return $this->fallbackAnalysis();
            }

            return [
                'subject' => $data['subject'] ?? 'Bilinmiyor',
                'outcome' => $data['outcome'] ?? 'Bilinmiyor',
                'ocr_text' => $data['ocr_text'] ?? '',
                'explanation' => $data['explanation'] ?? '',
                'correct_answer' => $data['correct_answer'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI service error', ['message' => $e->getMessage()]);
            return $this->fallbackAnalysis();
        }
    }

    /**
     * Generate questions for given outcomes using GPT-4o.
     */
    public function generateQuestions(array $outcomeNames, int $count = 5, string|int $gradeLevel = '7. Sınıf'): array
    {
        $outcomesText = implode(', ', $outcomeNames);

        $prompt = "Sen bir eğitim uzmanısın. Türkiye'deki {$gradeLevel}. sınıf müfredatına göre çoktan seçmeli sorular üretirsin.

Aşağıdaki kazanımlardan {$count} adet çoktan seçmeli soru üret:
Kazanımlar: {$outcomesText}

Her soru için şu formatta JSON array döndür:
[
  {
    \"question\": \"Soru metni\",
    \"options\": [\"A şıkkı\", \"B şıkkı\", \"C şıkkı\", \"D şıkkı\"],
    \"correct\": 0,
    \"outcome\": \"Kazanım adı\"
  }
]

correct alanı doğru cevabın 0-3 arası index numarasıdır.
Sadece JSON array döndür, başka bir şey yazma.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 3000,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI question generation error', ['body' => $response->body()]);
                return $this->fallbackQuestions($outcomeNames, $count);
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $questions = json_decode(trim($content), true);

            if (!$questions || !is_array($questions)) {
                return $this->fallbackQuestions($outcomeNames, $count);
            }

            return $questions;
        } catch (\Exception $e) {
            Log::error('OpenAI question generation error', ['message' => $e->getMessage()]);
            return $this->fallbackQuestions($outcomeNames, $count);
        }
    }

    /**
     * Generate custom questions by analyzing uploaded images.
     * Generates 9 similar questions per image.
     */
    public function generateTestFromImages(array $images, string|int $gradeLevel = '7. Sınıf'): array
    {
        $allQuestions = [];

        foreach ($images as $imagePath) {
            if (!$imagePath || !is_string($imagePath)) continue;

            $fullPath = storage_path('app/public/' . $imagePath);
            if (!file_exists($fullPath)) continue;

            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = mime_content_type($fullPath);
            $imageQuestions = [];
            $attempts = 0;

            while (count($imageQuestions) < 10 && $attempts < 3) {
                $attempts++;
                $needed = 10 - count($imageQuestions);

                $systemPrompt = "Sen uzman bir MEB LGS test hazırlama öğretmenisin. {$gradeLevel} müfredatına uygun beceri temelli (yeni nesil) sorular hazırlıyorsun.
Resimdeki soruyu incele. Aynı kazanım ve zorlukta ama farklı rakam/isim/hikaye ile tam {$needed} adet yeni çoktan seçmeli soru üret.

KURALLAR:
- Her soruyu çöz, doğru cevabı kontrol et. Yanlış cevaplı soru ASLA üretme.
- correct_answer: doğru şıkkın 0-3 indeksi
- Doğru cevabı farklı şıklara dağıt (hep A olmasın)
- Geometrik şekilleri sözel açıkla, SVG/HTML üretme

JSON formatı (sadece dizi döndür):
[{\"question_text\":\"...\",\"options\":[\"şık1\",\"şık2\",\"şık3\",\"şık4\"],\"correct_answer\":0}]

Tam {$needed} soru üret. Sadece JSON dizisi döndür.";

                try {
                    $response = Http::withToken($this->apiKey)
                        ->timeout(180)
                        ->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->model,
                            'messages' => [
                                ['role' => 'system', 'content' => $systemPrompt],
                                ['role' => 'user', 'content' => [
                                    ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$imageData}"]],
                                    ['type' => 'text', 'text' => "Bu soruyu incele ve {$needed} adet yeni MEB LGS tarzı benzer soru üret."],
                                ]],
                            ],
                            'max_tokens' => 16000,
                            'response_format' => ['type' => 'json_object'],
                        ]);

                    if ($response->failed()) {
                        Log::error('OpenAI generateTestFromImages error', ['body' => $response->body()]);
                        continue;
                    }

                    $finishReason = $response->json('choices.0.finish_reason', '');
                    $content = $response->json('choices.0.message.content', '');
                    $content = preg_replace('/```json\s*|\s*```/i', '', $content);
                    
                    // Try to extract JSON array from response
                    $questions = null;
                    
                    // First try: direct parse
                    $decoded = json_decode(trim($content), true);
                    if (is_array($decoded)) {
                        // If response is {"questions": [...]} wrapper, unwrap it
                        if (isset($decoded['questions']) && is_array($decoded['questions'])) {
                            $questions = $decoded['questions'];
                        } elseif (isset($decoded[0])) {
                            $questions = $decoded;
                        }
                    }
                    
                    // Second try: extract array from content  
                    if (!$questions && preg_match('/\[.*\]/s', $content, $matches)) {
                        $questions = json_decode($matches[0], true);
                    }

                    if ($questions && is_array($questions)) {
                        $imageQuestions = array_merge($imageQuestions, $questions);
                        Log::info("OpenAI generated questions", [
                            'attempt' => $attempts,
                            'requested' => $needed,
                            'received' => count($questions),
                            'total' => count($imageQuestions),
                            'finish_reason' => $finishReason
                        ]);
                    } else {
                        Log::error('OpenAI JSON parse error', ['content' => substr($content, 0, 500), 'finish_reason' => $finishReason]);
                    }
                } catch (\Exception $e) {
                    Log::error('OpenAI generateTestFromImages exception', ['message' => $e->getMessage()]);
                }
            }

            $allQuestions = array_merge($allQuestions, array_slice($imageQuestions, 0, 10));
        }

        return $allQuestions ?: $this->fallbackTestFromImages();
    }

    private function fallbackAnalysis(): array
    {
        return [
            'subject' => 'Matematik',
            'outcome' => 'Genel',
            'ocr_text' => 'AI analizi şu an kullanılamıyor.',
            'explanation' => 'OpenAI API bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.',
            'correct_answer' => null,
        ];
    }

    private function fallbackQuestions(array $outcomeNames, int $count): array
    {
        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $outcome = $outcomeNames[$i % count($outcomeNames)];
            $questions[] = [
                'question' => "({$outcome}) Bu bir örnek sorudur. Doğru cevabı seçiniz. (Soru #{$i})",
                'options' => ['Seçenek A', 'Seçenek B', 'Seçenek C', 'Seçenek D'],
                'correct' => 0,
                'outcome' => $outcome,
            ];
        }
        return $questions;
    }

    private function fallbackTestFromImages(): array
    {
        return [
            [
                'question_text' => "API Hatası: Yapay Zeka servisine ulaşılamadı. Lütfen tekrar deneyin.",
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 0,
            ]
        ];
    }
}
