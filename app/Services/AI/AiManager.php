<?php

namespace App\Services\AI;

use App\Models\AiProvider;
use App\Services\OpenAIService;
use App\Services\AI\GeminiService;
use Exception;

class AiManager
{
    /**
     * Resolves and returns the currently active AI Service from the database.
     * 
     * @return AiProviderInterface
     * @throws Exception if no active provider is found or identifier is unknown
     */
    public static function resolveActiveService(): AiProviderInterface
    {
        // For CLI or initial migrations where DB might not be ready, fallback safely
        try {
            $activeProvider = AiProvider::where('is_active', true)->first();
        } catch (Exception $e) {
            $activeProvider = null;
        }

        if (!$activeProvider) {
            // Fallback to OpenAI if database isn't configured yet
            return new OpenAIService(config('services.openai.api_key', env('OPENAI_API_KEY', '')));
        }

        switch ($activeProvider->identifier) {
            case 'openai':
                return new OpenAIService($activeProvider->api_key ?? '');
            case 'gemini':
                return new GeminiService($activeProvider->api_key ?? '');
            default:
                throw new Exception("Unknown AI Provider identifier: {$activeProvider->identifier}");
        }
    }
}
