<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(Request $request)
    {
        $validated = $request->validate([
            'city'=>['required','string']
        ]);

        $response = Http::get('https://api.openweathermap.org/data/2.5/weather',[
            'q'=>$validated['city'],
            'appid'=>config('services.openweather.key'),
            'units'=>'metric',
            'lang'=>'id'
        ]);

        if (!$response->successful()) {
            return response()->json(['status'=>'error','message'=>'City not found or API error'],404);
        }

        $data = $response->json();

        return response()->json([
            'status'=>'success',
            'data'=>[
                'city'=>$data['name'] ?? $validated['city'],
                'temperature'=>$data['main']['temp'] ?? null,
                'condition'=>$data['weather'][0]['description'] ?? null,
                'humidity'=>$data['main']['humidity'] ?? null,
                'wind_speed'=>$data['wind']['speed'] ?? null
            ]
        ]);
    }
}