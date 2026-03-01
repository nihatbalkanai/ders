<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Outcome;
use App\Models\Question;
use App\Models\Subject;
use App\Services\AI\AiManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{


    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'subject_id' => 'nullable|exists:subjects,id',
            'outcome_id' => 'nullable|exists:outcomes,id',
        ]);

        $path = $request->file('image')->store('questions', 'public');

        if ($request->has('subject_id') && $request->has('outcome_id')) {
            // Manual entry
            $question = Question::create([
                'user_id' => $request->user()->id,
                'image_path' => $path,
                'subject_id' => $request->subject_id,
                'outcome_id' => $request->outcome_id,
                'status' => 'analyzed',
            ]);

            $question->load(['subject', 'outcome']);

            return response()->json([
                'data' => [
                    'question' => $question,
                    'analysis' => [
                        'subject' => $question->subject->name,
                        'outcome' => $question->outcome->name,
                        'ocr_text' => '',
                        'explanation' => 'Manuel olarak ders seçildi.',
                        'correct_answer' => null
                    ],
                ],
            ]);
        }

        // Automatic entry via AI
        $aiService = AiManager::resolveActiveService();
        $analysis = $aiService->analyzeImage(Storage::disk('public')->path($path));

        $subject = Subject::firstOrCreate(['name' => $analysis['subject']]);
        $outcome = $subject->outcomes()->firstOrCreate(
            ['name' => $analysis['outcome']],
            ['grade_level' => $request->user()->grade_level]
        );

        $question = Question::create([
            'user_id' => $request->user()->id,
            'image_path' => $path,
            'subject_id' => $subject->id,
            'outcome_id' => $outcome->id,
            'ocr_text' => $analysis['ocr_text'],
            'ai_analysis' => $analysis,
            'correct_answer' => $analysis['correct_answer'] ?? null,
            'status' => 'analyzed',
        ]);

        $question->load(['subject', 'outcome']);

        return response()->json([
            'data' => [
                'question' => $question,
                'analysis' => [
                    'subject' => $subject->name,
                    'outcome' => $outcome->name,
                    'ocr_text' => $analysis['ocr_text'],
                    'explanation' => $analysis['explanation'],
                    'correct_answer' => $analysis['correct_answer'] ?? null,
                ],
            ],
        ]);
    }

    public function index(Request $request)
    {
        $questions = Question::where('user_id', $request->user()->id)
            ->with(['subject', 'outcome'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $questions]);
    }

    public function show(Request $request, $id)
    {
        $question = Question::where('user_id', $request->user()->id)
            ->with(['subject', 'outcome'])
            ->findOrFail($id);

        return response()->json(['data' => $question]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'outcome_id' => 'required|exists:outcomes,id',
        ]);

        $question = Question::where('user_id', $request->user()->id)->findOrFail($id);
        $question->update([
            'subject_id' => $request->subject_id,
            'outcome_id' => $request->outcome_id,
        ]);

        $question->load(['subject', 'outcome']);

        return response()->json([
            'message' => 'Soru başarıyla güncellendi.',
            'data' => $question
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $question = Question::where('user_id', $request->user()->id)->findOrFail($id);

        // Delete the image from storage if it exists
        if ($question->image_path && Storage::disk('public')->exists($question->image_path)) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return response()->json(['message' => 'Soru başarıyla silindi.']);
    }
}
