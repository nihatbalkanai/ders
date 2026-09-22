<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'is_active_text',
        'is_active_image',
        'identifier', // e.g. 'openai', 'gemini', 'deepseek'
    ];

    protected $casts = [
        'is_active_text' => 'boolean',
        'is_active_image' => 'boolean',
    ];
}
