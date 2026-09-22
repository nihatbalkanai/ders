<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use Illuminate\Http\Request;

class AiProviderController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => AiProvider::all()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'api_key' => 'nullable|string'
        ]);

        $provider = AiProvider::findOrFail($id);
        $provider->api_key = $request->api_key;
        $provider->save();

        return response()->json(['message' => 'API Anahtarı güncellendi.', 'data' => $provider]);
    }

    public function setActive(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|in:text,image'
        ]);

        $type = $request->type;
        $provider = AiProvider::findOrFail($id);

        if ($type === 'text') {
            AiProvider::where('id', '!=', $id)->update(['is_active_text' => false]);
            $provider->is_active_text = true;
        } else {
            AiProvider::where('id', '!=', $id)->update(['is_active_image' => false]);
            $provider->is_active_image = true;
        }
        
        $provider->save();

        $typeName = $type === 'text' ? 'Metin Üretimi' : 'Görsel Analiz';
        return response()->json([
            'message' => "{$provider->name}, {$typeName} için aktif edildi.", 
            'data' => AiProvider::all()
        ]);
    }
}
