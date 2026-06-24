<?php

namespace App\Services;

use App\Contracts\TravelMateServiceInterface;
use App\Models\Favorite;
use App\Models\SearchHistory;
use App\Models\TourismPlace;
use Illuminate\Support\Str;

class TravelMateService implements TravelMateServiceInterface
{
    public function getTourismPlacesByCity(string $city)
    {
        return TourismPlace::where('city', 'like', "%{$city}%")->get();
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

        if ($exists) {
            return null;
        }

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
            ->latest('updated_at')
            ->get();
    }

    public function saveSearchHistory(int $userId, string $keyword, string $type)
    {
        $normalizedKeyword = Str::squish($keyword);
        $normalizedType = Str::lower(Str::squish($type));

        $history = SearchHistory::updateOrCreate(
            [
                'user_id' => $userId,
                'keyword' => $normalizedKeyword,
                'type' => $normalizedType,
            ],
            [
                'updated_at' => now(),
            ]
        );

        // Pertahankan maksimal 20 keyword unik terbaru untuk setiap pengguna.
        $idsToKeep = SearchHistory::where('user_id', $userId)
            ->latest('updated_at')
            ->latest('id')
            ->limit(20)
            ->pluck('id');

        SearchHistory::where('user_id', $userId)
            ->whereNotIn('id', $idsToKeep)
            ->delete();

        return $history->fresh();
    }

    public function getSearchHistory(int $userId)
    {
        return SearchHistory::where('user_id', $userId)
            ->latest('updated_at')
            ->latest('id')
            ->take(20)
            ->get();
    }
}
