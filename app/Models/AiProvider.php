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
        'is_active',
        'identifier', // e.g. 'openai', 'gemini'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
