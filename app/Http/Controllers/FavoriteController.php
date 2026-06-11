<?php

namespace App\Http\Controllers;

use App\Contracts\TravelMateServiceInterface;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected TravelMateServiceInterface $travelMateService;

    public function __construct(TravelMateServiceInterface $travelMateService)
    {
        $this->travelMateService = $travelMateService;
    }

    public function index(Request $request)
    {
        $favorites = $this->travelMateService->getFavorites($request->user()->id);
        return response()->json(['status'=>'success','data'=>$favorites]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tourism_place_id'=>['required','exists:tourism_places,id'],
        ]);

        $fav = $this->travelMateService->addFavorite($request->user()->id, $validated['tourism_place_id']);

        if (!$fav) {
            return response()->json(['status'=>'error','message'=>'Already in favorites'],422);
        }

        return response()->json(['status'=>'success','message'=>'Added to favorites','data'=>$fav]);
    }

    public function destroy(Request $request, int $favoriteId)
    {
        $this->travelMateService->removeFavorite($request->user()->id, $favoriteId);
        return response()->json(['status'=>'success','message'=>'Removed from favorites']);
    }
}