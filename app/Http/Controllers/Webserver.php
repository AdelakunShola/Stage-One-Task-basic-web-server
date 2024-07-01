<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Webserver extends Controller
{
    public function greet(Request $request)
    {
        $visitorName = $request->query('visitor_name', 'Guest');
        $clientIp = $request->ip();

        // localhost case for testing
        if ($clientIp == '127.0.0.1' || $clientIp == '::1') {
            $clientIp = '127.0.0.1'; // Use a public IP for testing
        }

        // Use an IP geolocation API to get location data
        $geoResponse = Http::get("http://ip-api.com/json/{$clientIp}");
        $geoData = $geoResponse->json();

        $city = $geoData['city'] ?? 'Unknown';

        // weather API to get temperature data
        $weatherResponse = Http::get("http://api.openweathermap.org/data/2.5/weather", [
            'q' => $city,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]);
        $weatherData = $weatherResponse->json();

        $temperature = $weatherData['main']['temp'] ?? 'N/A';

        $responseData = [
            'client_ip' => $clientIp,
            'location' => $city,
            'greeting' => "Hello, {$visitorName}!, the temperature is {$temperature} degrees Celsius in {$city}"
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

