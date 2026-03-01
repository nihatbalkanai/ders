<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject_id', 'outcome_id',
        'image_path', 'ocr_text', 'ai_analysis', 'status',
    ];

    protected function casts(): array
    {
        return [
            'ai_analysis' => 'array',
        ];
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function outcome()
    {
        return $this->belongsTo(Outcome::class);
    }
}
