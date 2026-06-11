<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    public function index(Request $request)
    {
        $history = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string'],
            'type' => ['required', 'string'],
        ]);

        $history = SearchHistory::create([
            'user_id' => $request->user()->id,
            'keyword' => $validated['keyword'],
            'type' => $validated['type'],
        ]);

        // Hapus entry paling lama jika > 20
        $count = SearchHistory::where('user_id', $request->user()->id)->count();
        if ($count > 20) {
            SearchHistory::where('user_id', $request->user()->id)
                ->oldest()
                ->limit($count - 20)
                ->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Search history saved successfully',
            'data' => $history,
        ]);
    }
}