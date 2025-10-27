<?php
namespace App\Services\Features;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service class for interacting with the French National Address Database API (api-adresse.data.gouv.fr).
 *
 * This service provides methods to search for address information, primarily focusing
 * on retrieving a list of cities based on a given postal code.
 */
class AddressLookupService
{
    protected string $apiUrl = 'https://api-adresse.data.gouv.fr/search/';

    /**
     * Finds a list of cities associated with a given French postal code.
     *
     * It queries the api-adresse.data.gouv.fr API. If the postal code is not
     * 5 digits long, if the API request fails, or if no cities are found for the
     * given postal code, an empty array is returned.
     *
     * @param string $postalCode The 5-digit postal code to search for.
     * @return array<string, string> An associative array where both keys and values are city names (e.g., ['Lyon' => 'Lyon']). Returns an empty array on failure or if no cities are found.
     */
    public function findCitiesByPostalCode(string $postalCode): array
    {
        if (strlen($postalCode) !== 5) {
            return [];
        }

        try {
            $response = Http::get($this->apiUrl, [
                'q' => $postalCode,
                'type' => 'municipality',
                'limit' => 15,
            ]);

            if ($response->successful()) {
                return collect($response->json()['features'] ?? [])
                    ->map(fn ($feature) => [
                        'name' => $feature['properties']['city'],
                        'postcode' => $feature['properties']['postcode'],
                    ])
                    ->unique('name')
                    ->pluck('name', 'name')
                    ->toArray();
            }

            Log::error('Address API request failed', [
                'postal_code'   => $postalCode,
                'status'        => $response->status(),
                'body'          => $response->body()
            ]);
            return [];

        } catch (\Exception $e) {
            Log::error('Exception during Address API request', [
                'postal_code' => $postalCode,
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }
}
