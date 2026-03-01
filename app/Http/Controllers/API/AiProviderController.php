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

    public function setActive($id)
    {
        $provider = AiProvider::findOrFail($id);

        // Deactivate all others
        AiProvider::where('id', '!=', $id)->update(['is_active' => false]);
        
        // Activate chosen
        $provider->is_active = true;
        $provider->save();

        return response()->json(['message' => $provider->name . ' aktif edildi.', 'data' => AiProvider::all()]);
    }
}
