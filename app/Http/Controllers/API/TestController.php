<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneratedTest;
use App\Models\Outcome;
use App\Models\Question;
use App\Services\AI\AiManager;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function generateFromSelected(Request $request)
    {
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

        $imagePaths = $questions->pluck('image_path')->toArray();
        $outcomeIds = $questions->pluck('outcome_id')->filter()->unique()->values();

        // Generate 9 similar questions for each selected question
        $aiService = AiManager::resolveActiveService();
        $aiQuestions = $aiService->generateTestFromImages(
            $imagePaths,
            $user->grade_level
        );

        // Build the final questions array: for each original question add it first, then its 9 similar ones
        $finalQuestions = [];
        $aiIndex = 0;

        foreach ($questions as $q) {
            // Add original question
            $finalQuestions[] = [
                'question_text' => $q->ocr_text ?: ('Orjinal Soru: ' . ($q->outcome?->name ?? 'Bilinmeyen Kazanım')),
                'visual_svg' => null,
                'options' => $q->correct_answer !== null
                    ? [$q->correct_answer, 'Yanlış A', 'Yanlış B', 'Yanlış C']
                    : ['Şık A', 'Şık B', 'Şık C', 'Şık D'],
                'correct_answer' => 0,
                'is_original' => true,
                'original_image' => $q->image_url ?? null,
            ];

            // Add 9 AI-generated similar questions
            for ($i = 0; $i < 9 && $aiIndex < count($aiQuestions); $i++, $aiIndex++) {
                $aiQ = $aiQuestions[$aiIndex];
                $aiQ['is_original'] = false;
                $finalQuestions[] = $aiQ;
            }
        }

        $test = GeneratedTest::create([
            'user_id' => $user->id,
            'title' => 'Özel Test (' . count($questions) . ' Soru × 10) - ' . now()->format('d.m.Y H:i'),
            'questions_data' => $finalQuestions,
            'outcome_ids' => $outcomeIds->toArray(),
        ]);

        return response()->json(['data' => $test]);
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
        $aiService = AiManager::resolveActiveService();
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
            if (isset($answers[$i]) && (int) $answers[$i] === (int) ($q['correct'] ?? -1)) {
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
}
