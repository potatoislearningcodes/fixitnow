<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around OpenStreetMap's Nominatim public API
 * (https://nominatim.openstreetmap.org), used here for one-off address
 * lookups at booking time — not bulk geocoding, which Nominatim's usage
 * policy prohibits: https://operations.osmfoundation.org/policies/nominatim/
 *
 * Their policy requires a real identifying User-Agent and asks callers to
 * stay well under 1 request/second. Since this only fires once per booking
 * submission (a human action, not a loop), that's naturally satisfied here.
 */
class GeocodingService
{
    /**
     * Returns ['lat' => float, 'lng' => float] or null if the address
     * couldn't be resolved (bad address, service hiccup, etc). Never throws
     * — geocoding failing should never block a booking from being created.
     */
    public function geocode(string $address): ?array
    {
        try {
            $response = Http::withHeaders([
                // Required by Nominatim's usage policy — replace the email
                // with your own group's contact if you want bounce reports.
                'User-Agent' => 'FixItNow-StudentProject/1.0 (IPT2 school project; contact: admin@fixitnow.test)',
            ])
                ->withOptions([
                    // Many Windows PHP installs on student machines don't
                    // ship a CA certificate bundle, which breaks HTTPS
                    // verification entirely (cURL error 60). Skipping
                    // verification is acceptable here: this call only reads
                    // public map data and sends no sensitive information.
                    // If you'd rather fix it properly, install a CA bundle
                    // and set curl.cainfo in php.ini, then remove this line.
                    'verify' => false,
                ])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if (! $response->successful() || empty($response->json())) {
                return null;
            }

            $result = $response->json()[0];

            return [
                'lat' => (float) $result['lat'],
                'lng' => (float) $result['lon'],
            ];
        } catch (\Throwable $e) {
            // Log and move on — a down geocoding service is not a reason to
            // fail the whole booking request.
            Log::warning('Geocoding failed: '.$e->getMessage());

            return null;
        }
    }
}
