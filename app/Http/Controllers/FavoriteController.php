<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::with('tourismPlace')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $favorites,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tourism_place_id' => ['required', 'exists:tourism_places,id'],
        ]);

        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('tourism_place_id', $validated['tourism_place_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tourism place already added to favorites',
            ], 422);
        }

        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'tourism_place_id' => $validated['tourism_place_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tourism place added to favorites',
            'data' => $favorite,
        ]);
    }

    public function destroy(Request $request, Favorite $favorite)
    {
        abort_unless($favorite->user_id === $request->user()->id, 403);

        $favorite->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Favorite tourism place removed successfully',
        ]);
    }
}