<?php

namespace App\Contracts;

interface WeatherServiceInterface
{
    public function getCurrentWeatherByCity(string $city): array;
}
