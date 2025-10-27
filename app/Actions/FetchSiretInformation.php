<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchSiretInformation
{
    /**
     * Fetch legal information from INSEE SIRENE API
     *
     * @param string $siret The SIRET number (9 or 14 digits)
     * @return array|null Returns organization data or null if not found
     */
    public function execute(string $siret): ?array
    {
        // Clean and validate SIRET
        $siret = preg_replace('/\D/', '', $siret);

        if (strlen($siret) !== 14 && strlen($siret) !== 9) {
            return null;
        }

        // Call INSEE API
        $response = Http::withHeaders([
            'X-INSEE-Api-Key-Integration' => config('services.insee.key')
        ])->get("https://api.insee.fr/api-sirene/3.11/siret/{$siret}");

        if (!$response->ok()) {
            Log::error('SIRENE API error', [
                'status' => $response->status(),
                'siret' => $siret
            ]);
            return null;
        }

        $data = $response->json();

        // Parse SIRET data
        if (!empty($data['etablissement'])) {
            return $this->parseEtablissement($data['etablissement']);
        }

        // Parse SIREN data
        if (!empty($data['uniteLegale']['periodesUniteLegale'])) {
            return $this->parseUniteLegale($data['uniteLegale']);
        }

        return null;
    }

    /**
     * Parse establishment (SIRET) data
     */
    private function parseEtablissement(array $etablissement): array
    {
        $uniteLegale = $etablissement['uniteLegale'] ?? [];
        $periode = $etablissement['periodesEtablissement'][0] ?? [];
        $adresse = $etablissement['adresseEtablissement'] ?? [];

        return [
            'legal_siret' => $etablissement['siret'] ?? null,
            'legal_siren' => $etablissement['siren'] ?? null,
            'legal_created_at' => $periode['dateDebut']
                ?? $etablissement['dateCreationEtablissement']
                    ?? null,
            'legal_name' => $uniteLegale['denominationUniteLegale'] ?? null,
            'legal_activity_code' => $periode['activitePrincipaleEtablissement']
                ?? $uniteLegale['activitePrincipaleUniteLegale']
                    ?? null,
            'legal_is_ess' => ($uniteLegale['economieSocialeSolidaireUniteLegale'] ?? 'N') !== 'N',

            // Address fields
            'address_1' => trim(implode(' ', array_filter([
                $adresse['numeroVoieEtablissement'] ?? null,
                $adresse['indiceRepetitionEtablissement'] ?? null,
                $adresse['typeVoieEtablissement'] ?? null,
                $adresse['libelleVoieEtablissement'] ?? null,
            ]))),
            'address_2' => $adresse['complementAdresseEtablissement'] ?? null,
            'address_postal_code' => $adresse['codePostalEtablissement'] ?? null,
            'address_city' => $adresse['libelleCommuneEtablissement'] ?? null,
        ];
    }

    /**
     * Parse legal unit (SIREN) data
     */
    private function parseUniteLegale(array $uniteLegale): array
    {
        $periode = $uniteLegale['periodesUniteLegale'][0];

        return [
            'legal_siret' => null, // SIREN only, no specific establishment
            'legal_siren' => $uniteLegale['siren'] ?? null,
            'legal_created_at' => $periode['dateDebut']
                ?? $uniteLegale['dateCreationUniteLegale']
                    ?? null,
            'legal_name' => $periode['denominationUniteLegale'] ?? null,
            'legal_activity_code' => $periode['activitePrincipaleUniteLegale'] ?? null,
            'legal_is_ess' => ($periode['economieSocialeSolidaireUniteLegale'] ?? 'N') !== 'N',

            // No address for SIREN-level data
            'address_1' => null,
            'address_2' => null,
            'address_postal_code' => null,
            'address_city' => null,
        ];
    }
}
