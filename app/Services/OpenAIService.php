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
    private string $model = 'gpt-4o';

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
    public function generateQuestions(array $outcomeNames, int $count = 5, int $gradeLevel = 5): array
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
     */
    public function generateTestFromImages(array $imagePaths, string $gradeLevel = '7. Sınıf'): array
    {
        $systemPrompt = "Sen uzman bir eğitimci ve soru yazarısın. Türkiye'deki {$gradeLevel} müfredatına ve soru mantığına tam hakimsin.
1. Sana gönderilen orijinal soru fotoğraflarını incele. Orijinal sorulardaki denklemlerin, matematiksel/mantıksal yapının ve zorluk derecesinin ÖZÜNÜ kavra.
2. Ardından o mantığa tamamen uygun, yapı olarak benzer ama KESİNLİKLE YENİ 5 adet çoktan seçmeli soru üret.
3. ÇOK ÖNEMLİ: Eğer üreteceğin soru (orijinalinde olduğu gibi) geometrik bir şekil, bir tablo, bir şema veya herhangi bir grafiğe ihtiyaç duyuyorsa, bu çizimi \"visual_svg\" alanına geçerli, standart bir SVG kodu (vektörel çizim formunda `<svg>...</svg>`) olarak yaz! Eğer sorunun görsel bir çizime ihtiyacı yoksa (sadece metin yetiyorsa) bu alanı `null` bırak.
4. Çıktı formatı ŞU JSON DİZİSİ (Array) olmalıdır:
[
  {
    \"question\": \"Soru metni...\",
    \"visual_svg\": \"<svg viewBox='0 0 100 100'>...</svg>\" veya null,
    \"options\": [\"A şıkkı\", \"B şıkkı\", \"C şıkkı\", \"D şıkkı\"],
    \"correct\": 0
  }
]
correct alanı doğru cevabın index numarasıdır (0-A, 1-B vs). Sadece bu JSON dizisini döndür.";

        $contentArr = [
            [
                'type' => 'text',
                'text' => 'Aşağıdaki orijinal soruları analiz et ve kurallara uygun 5 YENİ benzer soru üret.'
            ]
        ];

        foreach ($imagePaths as $path) {
            $fullPath = Storage::disk('public')->path($path);
            if (file_exists($fullPath)) {
                $imageData = base64_encode(file_get_contents($fullPath));
                $mimeType = mime_content_type($fullPath);
                $contentArr[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:{$mimeType};base64,{$imageData}"
                    ]
                ];
            }
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(90) // Might take longer for multiple images
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $contentArr],
                    ],
                    'max_tokens' => 4000,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI generateTestFromImages error', ['body' => $response->body()]);
                return $this->fallbackTestFromImages();
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $questions = json_decode(trim($content), true);

            if (!$questions || !is_array($questions)) {
                return $this->fallbackTestFromImages();
            }

            return $questions;
        } catch (\Exception $e) {
            Log::error('OpenAI generateTestFromImages exception', ['message' => $e->getMessage()]);
            return $this->fallbackTestFromImages();
        }
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
                'question' => "API Hatası: Yapay Zeka servisine ulaşılamadı. Lütfen tekrar deneyin.",
                'visual_svg' => null,
                'options' => ['A', 'B', 'C', 'D'],
                'correct' => 0,
            ]
        ];
    }
}
