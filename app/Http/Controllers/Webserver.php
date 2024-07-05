<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Webserver extends Controller
{
    public function greet(Request $request)
    {
        // Get the visitor's name directly without any additional decoding
        $visitorName = $request->query('visitor_name', 'Guest');
        $visitorName = trim($visitorName, '"'); // Remove any extra quotes

        // Use ngrok headers to get the real client IP
        $clientIp = $request->header('X-Forwarded-For') ?: $request->ip();

        // Set custom headers for geolocation and weather requests
        $customHeaders = [
            'ngrok-skip-browser-warning' => 'any-value',
            'User-Agent' => 'CustomUserAgent/1.0'
        ];

        // Use an IP geolocation API to get location data
        $geoResponse = Http::withHeaders($customHeaders)->get("http://ip-api.com/json/{$clientIp}");
        $geoData = $geoResponse->json();

        $city = $geoData['city'] ?? 'Unknown';

        // Use a weather API to get temperature data
        $weatherResponse = Http::withHeaders($customHeaders)->get("http://api.openweathermap.org/data/2.5/weather", [
            'q' => $city,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]);
        $weatherData = $weatherResponse->json();

        $temperature = $weatherData['main']['temp'] ?? 'N/A';

        // Construct the response data
        $responseData = [
            'client_ip' => $clientIp,
            'location' => $city,
            'greeting' => "Hello {$visitorName}, the temperature is {$temperature} degrees Celsius in {$city}"
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
