<?php

namespace App\Http\Controllers;

use App\Models\TourismPlace;
use Illuminate\Http\Request;

class TourismController extends Controller
{
    // Daftar tempat wisata (filter kota)
    public function index(Request $request)
    {
        $city = $request->query('city');

        $query = TourismPlace::query();

        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }

        $data = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    // Detail tempat wisata
    public function show(TourismPlace $tourismPlace)
    {
        return response()->json([
            'status' => 'success',
            'data' => $tourismPlace,
        ]);
    }
}