<?php

namespace App\Services\AI;

interface AiProviderInterface
{
    /**
     * Resim analizi yaparak ders, kazanım, ocr metni ve açıklama çıkarır.
     * 
     * @param string $imagePath
     * @return array ['subject', 'outcome', 'ocr_text', 'explanation', 'correct_answer']
     */
    public function analyzeImage(string $imagePath): array;

    /**
     * Seçilen soruları analiz edip benzer/yeni sorular ve görsel gerekiyorsa SVG üretir.
     * 
     * @param array $images
     * @param string $gradeLevel
     * @return array Array of questions
     */
    public function generateTestFromImages(array $images, string $gradeLevel = '7. Sınıf'): array;

    /**
     * Metin tabanlı kazanımlar için soru üretir.
     * 
     * @param array $outcomeNames
     * @param int $count
     * @param string|int $gradeLevel
     * @return array
     */
    public function generateQuestions(array $outcomeNames, int $count = 5, string|int $gradeLevel = '7. Sınıf'): array;
}
