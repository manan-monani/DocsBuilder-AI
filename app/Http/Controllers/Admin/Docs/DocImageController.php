<?php

namespace App\Http\Controllers\Admin\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('docs/images', 'public');
            $url = Storage::url($path);

            return response()->json([
                'url' => $url,
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
