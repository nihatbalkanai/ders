<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model = 'deepseek-chat';
    protected string $baseUrl = 'https://api.deepseek.com/chat/completions';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Resim analizi yaparak ders, kazanım, vs çıkarır.
     * DeepSeek Vision modeli şu an yaygın değil, bu yüzden metin tabanlı fallback veya OpenAI yönlendirmesi mantıklı.
     * Ama Interface gereği bu metot var olmalı.
     */
    public function analyzeImage(string $imagePath): array
    {
        Log::warning('DeepSeek does not officially support image analysis in this integration. Returning fallback.');
        return $this->fallbackAnalysis();
    }

    /**
     * Resimlerden test üretme. DeepSeek resim desteklemediği için fallback döner.
     */
    public function generateTestFromImages(array $images, string|int $gradeLevel = '7. Sınıf'): array
    {
        Log::warning('DeepSeek does not officially support image-based generation in this integration. Returning fallback.');
        return $this->fallbackTestFromImages();
    }

    /**
     * Metin tabanlı kazanımlar için soru üretir.
     */
    public function generateQuestions(array $outcomeNames, int $count = 5, string|int $gradeLevel = '7. Sınıf'): array
    {
        $outcomesText = implode(', ', $outcomeNames);

        $prompt = "Sen bir eğitim uzmanısın. Türkiye'deki {$gradeLevel}. sınıf müfredatına göre çoktan seçmeli sorular üretirsin.

Aşağıdaki kazanımlardan {$count} adet çoktan seçmeli soru üret:
Kazanımlar: {$outcomesText}

Kurallar:
1. Sorular tamamen özgün ve müfredata uygun olmalıdır.
2. Her sorunun 4 şıkkı (A, B, C, D) olmalıdır.
3. Soruların doğru cevabı mantıksal olarak KESİN TEK BİR ŞIK olmalıdır. Doğru cevap (correct_answer) olarak şıkkın 0, 1, 2, 3 bazlı indeksini (A=0, B=1, vb.) döndür.
4. Şıkların sıralamasını (A, B, C, D) metin içinde kendin BAŞA EKLEME. Sadece şık metnini ver, harfi verme (örn: \"8\" yerine \"8\" yaz, \"A) 8\" yazma).
5. GÖRSEL/ŞEKİL YASAĞI (ÖNEMLİ): Soru içerisinde KESİNLİKLE SVG, HTML, shape_data veya herhangi bir çizim kodu ÜRETME. Eğer orijinal kazanım görsel veya şekil içeriyorsa, bu görseli soru metni (question_text) içerisinde SÖZEL OLARAK açıkla.
6. Yanıtı SADECE JSON formatında bir DİZİ ([ ... ]) olarak döndür. Markdown vs EKLEME.

Örnek JSON formatı:
[
  {
    \"question_text\": \"Soru metni... (Duruma göre şeklin sözel betimlemesi)\",
    \"options\": [\"Şık 1\", \"Şık 2\", \"Şık 3\", \"Şık 4\"],
    \"correct_answer\": 0
  }
]
Tam olarak {$count} soru üret ve sadece JSON dizisini döndür.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(120)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Sen Türkçe bilen uzman bir eğitmensin. Yalnızca istenilen JSON formatında çıktı verirsin.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 4000,
                    'temperature' => 0.7
                ]);

            if ($response->failed()) {
                Log::error('DeepSeek generateQuestions error', ['body' => $response->body()]);
                throw new \Exception("DeepSeek HTTP Fail: " . $response->body());
            }

            $content = $response->json('choices.0.message.content', '');
            
            // Temizleme: Eğer DeepSeek gereksiz metin döndürse diye
            $content = preg_replace('/```json\s*|\s*```/i', '', $content);
            if (preg_match('/\[.*\]/s', $content, $matches)) {
                $content = $matches[0];
            }
            
            $questions = json_decode(trim($content), true);

            if ($questions && is_array($questions)) {
                return $questions;
            } else {
                Log::error('DeepSeek JSON parse error', [
                    'content_received' => $content,
                    'json_error' => json_last_error_msg()
                ]);
                throw new \Exception("DeepSeek JSON Format Hatası.");
            }
        } catch (\Exception $e) {
            Log::error('DeepSeek exception', ['message' => $e->getMessage()]);
            // Fallback (basit yedek soru)
            return $this->fallbackQuestions($outcomeNames, $count);
        }
    }

    private function fallbackAnalysis(): array
    {
        return [
            'subject' => 'Matematik',
            'outcome' => 'Genel',
            'ocr_text' => 'AI analizi desteklenmiyor.',
            'explanation' => 'Bu AI modeli resim analizini desteklemiyor.',
            'correct_answer' => null,
        ];
    }

    private function fallbackTestFromImages(): array
    {
        return [
            [
                'question_text' => "Bu AI (DeepSeek) resimden soru üretmeyi desteklemiyor. Lütfen ayarları güncelleyin.",
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 0,
            ]
        ];
    }

    private function fallbackQuestions(array $outcomeNames, int $count): array
    {
        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $outcome = $outcomeNames[$i % count($outcomeNames)] ?? 'Genel';
            $questions[] = [
                'question_text' => "({$outcome}) DeepSeek API bağlantı hatası. (Soru #{$i})",
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 0,
            ];
        }
        return $questions;
    }
}
