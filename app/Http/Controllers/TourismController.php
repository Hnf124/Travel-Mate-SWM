<?php

namespace App\Http\Controllers;

use App\Contracts\TravelMateServiceInterface;
use Illuminate\Http\Request;

class TourismController extends Controller
{
    protected TravelMateServiceInterface $travelMateService;

    public function __construct(TravelMateServiceInterface $travelMateService)
    {
        $this->travelMateService = $travelMateService;
    }

    public function index(Request $request)
    {
        $city = $request->query('city', '');
        $places = $this->travelMateService->getTourismPlacesByCity($city);

        return response()->json([
            'status' => 'success',
            'data' => $places,
        ]);
    }

    public function show(int $id)
    {
        $place = $this->travelMateService->getTourismPlaceDetail($id);

        return response()->json([
            'status' => 'success',
            'data' => $place,
        ]);
    }
}