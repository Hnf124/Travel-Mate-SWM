<?php

namespace App\Services;

use App\Contracts\TravelMateServiceInterface;
use App\Models\TourismPlace;
use App\Models\Favorite;
use App\Models\SearchHistory;

class TravelMateService implements TravelMateServiceInterface
{
    public function getTourismPlacesByCity(string $city)
    {
        return TourismPlace::where('city', 'like', "%$city%")->get();
    }

    public function getTourismPlaceDetail(int $id)
    {
        return TourismPlace::findOrFail($id);
    }

    public function addFavorite(int $userId, int $tourismPlaceId)
    {
        $exists = Favorite::where('user_id', $userId)
            ->where('tourism_place_id', $tourismPlaceId)
            ->exists();

        if ($exists) return null; // sudah ada

        return Favorite::create([
            'user_id' => $userId,
            'tourism_place_id' => $tourismPlaceId,
        ]);
    }

    public function removeFavorite(int $userId, int $tourismPlaceId)
    {
        return Favorite::where('user_id', $userId)
            ->where('tourism_place_id', $tourismPlaceId)
            ->delete();
    }

    public function getFavorites(int $userId)
    {
        return Favorite::with('tourismPlace')
            ->where('user_id', $userId)
            ->get();
    }

    public function saveSearchHistory(int $userId, string $keyword, string $type)
    {
        $history = SearchHistory::create([
            'user_id' => $userId,
            'keyword' => $keyword,
            'type' => $type,
        ]);

        // pastikan maksimal 20 entry per user
        $count = SearchHistory::where('user_id', $userId)->count();
        if ($count > 20) {
            SearchHistory::where('user_id', $userId)
                ->oldest()
                ->limit($count - 20)
                ->delete();
        }

        return $history;
    }

    public function getSearchHistory(int $userId)
    {
        return SearchHistory::where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get();
    }
}