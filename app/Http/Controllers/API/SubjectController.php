<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('is_active', true)->get();
        return response()->json(['data' => $subjects]);
    }

    public function outcomes($id)
    {
        $subject = Subject::findOrFail($id);
        $outcomes = $subject->outcomes()->get();
        return response()->json(['data' => $outcomes]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
        ]);

        $subject = Subject::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json(['data' => $subject], 201);
    }

    public function storeOutcome(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:1000',
        ]);

        $outcome = $subject->outcomes()->create([
            'name' => $validated['name'],
            'grade_level' => $request->user()->grade_level ?? 5, // Default to user's grade or 5
        ]);

        return response()->json(['data' => $outcome], 201);
    }
}
