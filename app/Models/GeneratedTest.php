<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'questions_data',
        'outcome_ids', 'user_answers', 'score', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'questions_data' => 'array',
            'outcome_ids' => 'array',
            'user_answers' => 'array',
            'score' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
