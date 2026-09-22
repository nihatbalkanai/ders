<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneratedTest;
use App\Models\Outcome;
use App\Models\Question;
use App\Services\AI\AiManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function generateFromSelected(Request $request)
    {
        set_time_limit(600); // 10 dakika - AI test üretimi uzun sürebilir
        $user = $request->user();

        $validated = $request->validate([
            'question_ids' => 'required|array|min:1'
        ]);

        $questions = Question::where('user_id', $user->id)
            ->whereIn('id', $validated['question_ids'])
            ->whereNotNull('image_path')
            ->with(['subject', 'outcome'])
            ->get();

        if ($questions->isEmpty()) {
            return response()->json([
                'message' => 'Geçerli resimli soru bulunamadı.',
            ], 422);
        }

        $aiService = AiManager::resolveImageService();
        $gradeLevel = $user->grade_level . '. Sınıf';
        $createdTests = [];

        // Her seçilen soru için ayrı bir test oluştur (10'ar soru)
        foreach ($questions as $q) {
            try {
                Log::info('Test generation starting for question', ['question_id' => $q->id, 'grade' => $gradeLevel]);

                $aiQuestions = $aiService->generateTestFromImages(
                    [$q->image_path],
                    $gradeLevel
                );

                Log::info('Test generation result for question', ['question_id' => $q->id, 'question_count' => count($aiQuestions)]);

                $finalQuestions = [];
                foreach ($aiQuestions as $aiQ) {
                    $aiQ['is_original'] = false;
                    $finalQuestions[] = $aiQ;
                }

                $outcomeName = $q->outcome?->name ?? 'Genel';
                $subjectName = $q->subject?->name ?? 'Ders';

                $test = GeneratedTest::create([
                    'user_id' => $user->id,
                    'title' => $subjectName . ' - ' . $outcomeName . ' (10 Soru) - ' . now()->format('d.m.Y H:i'),
                    'questions_data' => $finalQuestions,
                    'outcome_ids' => $q->outcome_id ? [$q->outcome_id] : [],
                ]);

                $createdTests[] = $test;
            } catch (\Exception $e) {
                Log::error('Test generation failed for question', ['question_id' => $q->id, 'error' => $e->getMessage()]);
                // Hata olsa bile diğer sorular için devam et
            }
        }

        if (empty($createdTests)) {
            return response()->json(['message' => 'Hiçbir test oluşturulamadı. Lütfen tekrar deneyin.'], 500);
        }

        return response()->json(['data' => $createdTests]);
    }

    public function generate(Request $request)
    {
        $user = $request->user();

        // Get user's weak outcomes (outcomes from uploaded questions)
        $outcomeIds = Question::where('user_id', $user->id)
            ->whereNotNull('outcome_id')
            ->pluck('outcome_id')
            ->unique()
            ->values();

        if ($outcomeIds->isEmpty()) {
            return response()->json([
                'message' => 'Henüz analiz edilmiş sorun yok. Önce birkaç soru yükle!',
            ], 422);
        }

        $outcomes = Outcome::whereIn('id', $outcomeIds)->get();
        $outcomeNames = $outcomes->pluck('name')->toArray();

        // Generate questions via AI (Assuming the active provider supports this method)
        // If not, we fallback to OpenAI or throw
        $aiService = AiManager::resolveTextService();
        if (method_exists($aiService, 'generateQuestions')) {
            $questions = $aiService->generateQuestions(
                $outcomeNames,
                5,
                $user->grade_level
            );
        } else {
            // Temporary stub if method missing on interface
            $questions = [];
        }

        $test = GeneratedTest::create([
            'user_id' => $user->id,
            'title' => 'Kişisel Test - ' . now()->format('d.m.Y H:i'),
            'questions_data' => $questions,
            'outcome_ids' => $outcomeIds->toArray(),
        ]);

        return response()->json(['data' => $test]);
    }

    public function index(Request $request)
    {
        $tests = GeneratedTest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $tests]);
    }

    public function show(Request $request, $id)
    {
        $test = GeneratedTest::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $test]);
    }

    public function submit(Request $request, $id)
    {
        $test = GeneratedTest::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($test->completed_at) {
            return response()->json(['message' => 'Bu test zaten tamamlanmış.'], 422);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $questions = $test->questions_data;
        $answers = $validated['answers'];
        $correct = 0;

        foreach ($questions as $i => $q) {
            $correctAnswer = $q['correct_answer'] ?? $q['correct'] ?? -1;
            if (isset($answers[$i]) && (int) $answers[$i] === (int) $correctAnswer) {
                $correct++;
            }
        }

        $total = count($questions);
        $score = $total > 0 ? round(($correct / $total) * 100) : 0;

        $test->update([
            'user_answers' => $answers,
            'score' => $score,
            'completed_at' => now(),
        ]);

        return response()->json(['data' => $test->fresh()]);
    }

    public function destroy(Request $request, $id)
    {
        $test = GeneratedTest::where('user_id', $request->user()->id)->findOrFail($id);
        $test->delete();

        return response()->json(['message' => 'Test başarıyla silindi.']);
    }

    /**
     * Save AI-generated image URL for a specific question in a test.
     * Called by Puter.js after generating an image on the client side.
     */
    public function saveQuestionImage(Request $request, $id)
    {
        $validated = $request->validate([
            'question_index' => 'required|integer|min:0',
            'image_url' => 'required|string',
        ]);

        $test = GeneratedTest::where('user_id', $request->user()->id)->findOrFail($id);
        
        $questionsData = $test->questions_data;
        if (!is_array($questionsData)) {
            $questionsData = json_decode($questionsData, true);
        }

        $index = $validated['question_index'];
        if (isset($questionsData[$index])) {
            $questionsData[$index]['ai_generated_image_url'] = $validated['image_url'];
            $test->questions_data = $questionsData;
            $test->save();

            return response()->json(['message' => 'Resim URL kaydedildi.']);
        }

        return response()->json(['message' => 'Soru bulunamadı.'], 404);
    }
}
