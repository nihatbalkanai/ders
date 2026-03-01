<?php

namespace Database\Seeders;

use App\Models\Outcome;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Matematik',
                'icon' => '📐',
                'description' => 'Sayılar, geometri, cebir ve problem çözme',
                'outcomes' => [
                    ['name' => 'Doğal Sayılar', 'grades' => [5, 6]],
                    ['name' => 'Tam Sayılar', 'grades' => [6, 7]],
                    ['name' => 'Rasyonel Sayılar', 'grades' => [7, 8]],
                    ['name' => 'Çarpanlar ve Katlar', 'grades' => [5, 6]],
                    ['name' => 'Kesirler', 'grades' => [5, 6, 7]],
                    ['name' => 'Ondalık Gösterim', 'grades' => [5, 6]],
                    ['name' => 'Yüzde Hesaplamaları', 'grades' => [6, 7]],
                    ['name' => 'Oran ve Orantı', 'grades' => [6, 7]],
                    ['name' => 'Cebirsel İfadeler', 'grades' => [6, 7, 8]],
                    ['name' => 'Denklem Çözme', 'grades' => [7, 8]],
                    ['name' => 'Eşitsizlikler', 'grades' => [8]],
                    ['name' => 'Üslü İfadeler', 'grades' => [8]],
                    ['name' => 'Kareköklü İfadeler', 'grades' => [8]],
                    ['name' => 'Üçgenler', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Dörtgenler', 'grades' => [5, 6, 7]],
                    ['name' => 'Çember ve Daire', 'grades' => [6, 7]],
                    ['name' => 'Alan ve Çevre', 'grades' => [5, 6, 7]],
                    ['name' => 'Hacim Hesaplamaları', 'grades' => [6, 7, 8]],
                    ['name' => 'Koordinat Sistemi', 'grades' => [7, 8]],
                    ['name' => 'Olasılık', 'grades' => [8]],
                    ['name' => 'İstatistik', 'grades' => [7, 8]],
                    ['name' => 'Örüntü ve İlişki', 'grades' => [5, 6]],
                ],
            ],
            [
                'name' => 'Türkçe',
                'icon' => '📖',
                'description' => 'Okuma anlama, dil bilgisi ve yazılı anlatım',
                'outcomes' => [
                    ['name' => 'Okuduğunu Anlama', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Sözcükte Anlam', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Cümlede Anlam', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Paragrafta Anlam', 'grades' => [6, 7, 8]],
                    ['name' => 'Yazım Kuralları', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Noktalama İşaretleri', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Fiiller', 'grades' => [6, 7, 8]],
                    ['name' => 'İsimler ve Sıfatlar', 'grades' => [5, 6]],
                    ['name' => 'Zarflar', 'grades' => [6, 7]],
                    ['name' => 'Cümle Bilgisi', 'grades' => [7, 8]],
                    ['name' => 'Söz Sanatları', 'grades' => [7, 8]],
                    ['name' => 'Metin Türleri', 'grades' => [6, 7, 8]],
                ],
            ],
            [
                'name' => 'İngilizce',
                'icon' => '🌍',
                'description' => 'İngilizce dil becerileri ve gramer',
                'outcomes' => [
                    ['name' => 'Simple Present Tense', 'grades' => [5, 6]],
                    ['name' => 'Present Continuous Tense', 'grades' => [5, 6]],
                    ['name' => 'Simple Past Tense', 'grades' => [6, 7]],
                    ['name' => 'Future Tense', 'grades' => [7, 8]],
                    ['name' => 'Comparatives & Superlatives', 'grades' => [7, 8]],
                    ['name' => 'Reading Comprehension', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Vocabulary', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Modals', 'grades' => [7, 8]],
                    ['name' => 'Prepositions', 'grades' => [5, 6]],
                    ['name' => 'Conditionals', 'grades' => [8]],
                ],
            ],
            [
                'name' => 'Fen Bilimleri',
                'icon' => '🔬',
                'description' => 'Fizik, kimya, biyoloji ve yer bilimleri',
                'outcomes' => [
                    ['name' => 'Madde ve Değişim', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Kuvvet ve Hareket', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Enerji', 'grades' => [6, 7, 8]],
                    ['name' => 'Işık ve Ses', 'grades' => [5, 6, 7]],
                    ['name' => 'Elektrik', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Canlılar Dünyası', 'grades' => [5, 6]],
                    ['name' => 'Vücudumuzdaki Sistemler', 'grades' => [6, 7]],
                    ['name' => 'Hücre', 'grades' => [7, 8]],
                    ['name' => 'DNA ve Genetik', 'grades' => [8]],
                    ['name' => 'Dünya ve Evren', 'grades' => [5, 6, 7]],
                    ['name' => 'Basınç', 'grades' => [8]],
                    ['name' => 'Asit ve Bazlar', 'grades' => [8]],
                ],
            ],
            [
                'name' => 'Sosyal Bilgiler',
                'icon' => '🌎',
                'description' => 'Tarih, coğrafya ve vatandaşlık',
                'outcomes' => [
                    ['name' => 'Türk Tarihi', 'grades' => [5, 6, 7]],
                    ['name' => 'İnkılap Tarihi', 'grades' => [8]],
                    ['name' => 'Coğrafya', 'grades' => [5, 6, 7]],
                    ['name' => 'Vatandaşlık', 'grades' => [5, 6, 7, 8]],
                    ['name' => 'Ekonomi', 'grades' => [6, 7]],
                    ['name' => 'Kültür ve Miras', 'grades' => [5, 6]],
                ],
            ],
        ];

        foreach ($subjects as $subjectData) {
            $subject = Subject::create([
                'name' => $subjectData['name'],
                'icon' => $subjectData['icon'],
                'description' => $subjectData['description'],
            ]);

            foreach ($subjectData['outcomes'] as $outcomeData) {
                foreach ($outcomeData['grades'] as $grade) {
                    Outcome::create([
                        'subject_id' => $subject->id,
                        'name' => $outcomeData['name'],
                        'grade_level' => $grade,
                        'code' => strtoupper(substr($subjectData['name'], 0, 3)) . ".{$grade}." . substr(md5($outcomeData['name'] . $grade), 0, 4),
                    ]);
                }
            }
        }
    }
}
