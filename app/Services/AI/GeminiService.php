<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AiProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    
    // We use gemini-2.0-flash as the API key only supports 2.0+ models
    protected string $model = 'gemini-2.0-flash';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function analyzeImage(string $imagePath): array
    {
        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $prompt = "Sen bir eğitim soru analiz asistanısın. Türkiye'deki 5-8. sınıf müfredatına göre soruları analiz edersin.
Verilen soru fotoğrafını analiz et ve şu bilgileri JSON olarak döndür:
1. \"subject\": Sorunun ait olduğu ders adı (örneğin: Matematik, Türkçe, Fen Bilimleri)
2. \"outcome\": Sorunun ait olduğu kazanım adı (örneğin: Kesirlerle İşlemler, Fiiller)
3. \"ocr_text\": Sorudaki metin (OCR)
4. \"explanation\": Kısa analiz açıklaması
5. \"correct_answer\": Sorunun doğru cevabı (A, B, C veya D). Eğer soruda şık yoksa veya bulunamıyorsa boş bırak.

Sadece JSON döndür, markdown formatı veya başka metin ekleme.";

            $response = Http::post("{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                // Force JSON response
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                
                // Gemini sometimes wraps in ```json ... ``` despite mimeType constraint, clean it
                $content = preg_replace('/```json\s*/', '', $content);
                $content = preg_replace('/```\s*/', '', $content);
                
                $result = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $result;
                }
            }

            Log::error('Gemini API Error in analyzeImage', ['response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error("Gemini API Exception in analyzeImage: " . $e->getMessage());
        }

        return [
            'subject' => 'Bilinmeyen Ders',
            'outcome' => 'Bilinmeyen Kazanım',
            'ocr_text' => 'Okunamadı',
            'explanation' => 'Görsel analiz edilemedi.',
            'correct_answer' => null
        ];
    }

    public function generateTestFromImages(array $images, string $gradeLevel = '7. Sınıf'): array
    {
        $allQuestions = [];

        foreach ($images as $imagePath) {
            if (!file_exists($imagePath)) continue;

            try {
                $parts = [];

                $prompt = "Sen uzman bir test hazırlama öğretmenisin. {$gradeLevel} müfredatına uygun sorular hazırlıyorsun.
Sana verilen resimdeki orjinal sorunun mantığını, zorluk derecesini, kazanımını ve konu içeriğini incele.
Bu soruya benzeyen, aynı kazanıma yönelik, ancak RAKAMLARI, İSİMLERİ VEYA BİÇİMİ FARKLI olan 9 adet YENİ çoktan seçmeli (A, B, C, D) soru hazırla.

Sorular orijinalden farklı olmalı ama aynı seviyede ve aynı konuda olmalı.

Eğer orijinal soru şekilli, grafikli veya resimliyse, yeni hazırlayacağın soru için de uygun bir görsel kodunu 'visual_svg' alanına GÜVENLİ VE GEÇERLİ BİR SVG KODU OLARAK ekle. Eğer görsel gerekmiyorsa 'visual_svg' alanını null bırak.
Oluşturduğun SVG, <svg viewBox=\"0 0 400 200\" xmlns=\"http://www.w3.org/2000/svg\">... formatında olmalı.

Sadece JSON formatında bir dizi (array) döndür. Başka hiçbir metin veya markdown yazma. JSON formatı şöyle olmalı:
[
  {
    \"question_text\": \"Soru metni...\",
    \"visual_svg\": \"<svg...>...</svg>\" veya null,
    \"options\": [\"A şıkkı\", \"B şıkkı\", \"C şıkkı\", \"D şıkkı\"],
    \"correct_answer\": 0
  }
]

Tam olarak 9 soru üret.";
                $parts[] = ['text' => $prompt];

                $parts[] = [
                    'inline_data' => [
                        'mime_type' => mime_content_type($imagePath),
                        'data' => base64_encode(file_get_contents($imagePath))
                    ]
                ];

                $response = Http::timeout(120)->post("{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => $parts
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ]
                ]);

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');

                    // Clean markdown artifacts
                    $content = preg_replace('/```json\s*/', '', $content);
                    $content = preg_replace('/```\s*/', '', $content);

                    $result = json_decode($content, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($result)) {
                        $allQuestions = array_merge($allQuestions, $result);
                    }
                } else {
                    Log::error('Gemini API Error in generateTestFromImages', ['response' => $response->json()]);
                }
            } catch (\Exception $e) {
                Log::error("Gemini API Exception in generateTestFromImages: " . $e->getMessage());
            }
        }

        return $allQuestions;
    }
}
