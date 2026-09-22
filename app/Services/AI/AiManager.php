<?php

namespace App\Services\AI;

use App\Models\AiProvider;
use App\Services\OpenAIService;
use Exception;

class AiManager
{
    /**
     * Resolves and returns the AI Service configured for Text Generation.
     * 
     * @return AiProviderInterface
     */
    public static function resolveTextService(): AiProviderInterface
    {
        try {
            $activeProvider = AiProvider::where('is_active_text', true)->first();
        } catch (Exception $e) {
            $activeProvider = null;
        }

        if (!$activeProvider) {
            return new OpenAIService(config('services.openai.api_key', env('OPENAI_API_KEY', '')));
        }

        return self::instantiateProvider($activeProvider);
    }

    /**
     * Resolves and returns the AI Service configured for Image Generation/Analysis.
     * 
     * @return AiProviderInterface
     */
    public static function resolveImageService(): AiProviderInterface
    {
        try {
            $activeProvider = AiProvider::where('is_active_image', true)->first();
        } catch (Exception $e) {
            $activeProvider = null;
        }

        if (!$activeProvider) {
            return new OpenAIService(config('services.openai.api_key', env('OPENAI_API_KEY', '')));
        }

        return self::instantiateProvider($activeProvider);
    }

    private static function instantiateProvider(AiProvider $provider): AiProviderInterface
    {
        switch ($provider->identifier) {
            case 'openai':
                return new OpenAIService($provider->api_key ?? '');
            case 'deepseek':
                return new DeepSeekService($provider->api_key ?? '');
            default:
                throw new Exception("Unknown AI Provider identifier: {$provider->identifier}");
        }
    }
}
