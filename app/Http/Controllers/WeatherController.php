<?php

namespace App\Http\Controllers;

use App\Contracts\WeatherServiceInterface;
use App\Exceptions\WeatherServiceException;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherServiceInterface $weatherService
    ) {}

    public function show(Request $request)
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
        ]);

        try {
            $weather = $this->weatherService->getCurrentWeatherByCity(
                $validated['city']
            );

            return response()->json([
                'status' => 'success',
                'data' => $weather,
            ]);
        } catch (WeatherServiceException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->statusCode());
        }
    }
}
