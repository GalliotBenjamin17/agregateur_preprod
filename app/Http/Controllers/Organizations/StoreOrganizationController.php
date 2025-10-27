<?php

namespace App\Http\Controllers\Organizations;

use Akaunting\Setting\Support\Arr;
use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StoreOrganizationController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'siret' => 'required|unique:organizations,legal_siret',
            'organization_type_id' => 'required',
            'tenant_id' => 'required',
        ]);

        $validated['siret'] = str_replace(' ', '', $validated['siret']);

        if ($request->get('siret')) {
            $status = $this->getSiretInformations($validated, $request->get('siret'));

            if ($status == Command::INVALID) {
                \Session::flash('alert', "Nous n'avons pas trouvé d'organisation sur ce SIRET.");

                return back();
            }
        }

        $validated['created_by'] = $request->user()->id;

        $organization = Organization::create($validated);

        ActivityHelper::push(
            performedOn: $organization,
            title: "Ajout d'une nouvelle entité",
            description: 'Entité : '.$organization->name,
            url: route('organizations.show', ['organization' => $organization->slug])
        );

        \Session::flash('success', "L'entité a été ajouté sur la plateforme.");

        return back();
    }

    public function getSiretInformations(array &$validated, string $siret): int
    {
        $siret = preg_replace('/\D/', '', $siret);

        if (strlen($siret) !== 14) {
            \Session::flash('alert', "Le SIRET doit contenir 14 chiffres.");
            return Command::INVALID;
        }

        $siretRequest = Http::withHeaders([
            'X-INSEE-Api-Key-Integration' => '1d5580be-7b60-4f88-9580-be7b60af888a'
        ])->get('https://api.insee.fr/api-sirene/3.11/siret/'.$siret);

        if (! $siretRequest->ok()) {
            \Log::error('SIRENE API HTTP error', [
                'status' => $siretRequest->status(),
                'body' => $siretRequest->body()
            ]);
            return Command::INVALID;
        }

        $json = $siretRequest->json();

        // SIRET
        if (!empty($json['etablissement'])) {
            $etablissement = $json['etablissement'];
            $uniteLegale = $etablissement['uniteLegale'] ?? [];
            $periode = $etablissement['periodesEtablissement'][0] ?? [];

            $validated['legal_siret'] = $etablissement['siret'] ?? null;
            $validated['legal_siren'] = $etablissement['siren'] ?? null;
            $validated['legal_created_at'] = $periode['dateDebut'] ?? $etablissement['dateCreationEtablissement'] ?? null;
            $validated['legal_name'] = $uniteLegale['denominationUniteLegale'] ?? null;
            $validated['legal_activity_code'] = $periode['activitePrincipaleEtablissement'] ?? $uniteLegale['activitePrincipaleUniteLegale'] ?? null;
            $validated['legal_is_ess'] = ($uniteLegale['economieSocialeSolidaireUniteLegale'] ?? 'N') !== 'N';

            $adresse = $etablissement['adresseEtablissement'] ?? [];
            $validated['address_1'] = implode(' ', array_filter([
                $adresse['numeroVoieEtablissement'] ?? null,
                $adresse['indiceRepetitionEtablissement'] ?? null,
                $adresse['typeVoieEtablissement'] ?? null,
                $adresse['libelleVoieEtablissement'] ?? null,
            ]));
            $validated['address_2'] = $adresse['complementAdresseEtablissement'] ?? null;
            $validated['address_postal_code'] = $adresse['codePostalEtablissement'] ?? null;
            $validated['address_city'] = $adresse['libelleCommuneEtablissement'] ?? null;

            return Command::SUCCESS;
        }

        // SIREN
        if (!empty($json['uniteLegale']['periodesUniteLegale'])) {
            $uniteLegale = $json['uniteLegale'];
            $periode = $uniteLegale['periodesUniteLegale'][0];

            $validated['legal_siret'] = $periode['siret'] ?? null;
            $validated['legal_siren'] = $uniteLegale['siren'] ?? null;
            $validated['legal_created_at'] = $periode['dateDebut'] ?? $uniteLegale['dateCreationUniteLegale'] ?? null;
            $validated['legal_name'] = $periode['denominationUniteLegale'] ?? null;
            $validated['legal_activity_code'] = $periode['activitePrincipaleUniteLegale'] ?? null;
            $validated['legal_is_ess'] = ($periode['economieSocialeSolidaireUniteLegale'] ?? 'N') !== 'N';

            return Command::SUCCESS;
        }

        return Command::INVALID;
    }
}
