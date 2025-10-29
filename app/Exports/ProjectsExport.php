<?php

namespace App\Exports;

use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProjectsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison, WithMultipleSheets, WithTitle
{
    use Exportable;

    public function collection()
    {
        return Project::with([
            'tenant',
            'donationSplits',
            'certification',
            'sponsor',
            'segmentation',
            'referent',
            'auditor',
            'parentProject',
            'methodForm',
            'activeCarbonPrice',
        ])->withCount(['childrenProjects'])
            ->whereNull('parent_project_id')
            ->get();
    }

    public function map($row): array
    {
        $contribTtc = (float) $row->donationSplits->sum('amount');
        $contribHt = TVAHelper::getHT($contribTtc);
        $costHt = TVAHelper::getHT((float) $row->cost_global_ttc);
        $financingRatio = ((float) $row->cost_global_ttc > 0) ? ($contribTtc / (float) $row->cost_global_ttc) : 0.0;

        $objectiveTco2 = $row->hasChildrenProjects()
            ? ((bool) $row->is_goal_tco2_edited_manually ? $row->tco2 : $row->childrenProjects()->sum('tco2'))
            : $row->tco2;

        $sponsorType = match ($row->sponsor ? get_class($row->sponsor) : null) {
            Organization::class => 'Organisation',
            User::class => 'Particulier',
            default => 'Inconnu',
        };
        $sponsorName = match ($row->sponsor ? get_class($row->sponsor) : null) {
            Organization::class, User::class => $row->sponsor?->name ?? '',
            default => 'Inconnu',
        };
        $sponsorEmail = match ($row->sponsor ? get_class($row->sponsor) : null) {
            Organization::class => $row->sponsor?->billing_email ?? '',
            User::class => $row->sponsor?->email ?? '',
            default => 'Inconnu',
        };

        return [
            $row->id,
            $row->name,
            $row->state->humanName(),
            $row->methodForm?->name ?? '',
            $row->certification_state->humanName(),
            $row->certification?->name ?? '',
            $row->segmentation?->name ?? '',
            $objectiveTco2,
            $row->donationSplits->sum('tonne_co2'),
            $financingRatio,
            $contribTtc,
            $contribHt,
            $row->cost_global_ttc,
            $costHt,
            $row->activeCarbonPrice?->price,
            $row->cost_duration_years,
            $row->address_1,
            $row->address_postal_code,
            $row->address_city,
            $sponsorType,
            $sponsorName,
            ($row->sponsor instanceof Organization) ? $row->sponsor?->siret : '',
            $sponsorEmail,
            $row->referent?->name ?? '',
            $row->referent?->email ?? '',
            $row->tenant?->name,
        ];
    }

    public function headings(): array
    {
        return [
            'ID projet','Nom','Statut principal','Méthode','Statut méthode','Label','Segmentation',
            'Objectif tCO2','Contributions tCO2','Pourcentage de financement',
            'Contributions TTC €','Contributions HT €','Coût global TTC','Coût global HT','Prix crédit carbone HT plateforme (actuel)',
            'Durée du projet (années)','Adresse','Code postal','Ville',
            'Type de porteur','Porteur','SIRET porteur','Mail du porteur','Référent','Mail du référent','Instance locale',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_PERCENTAGE_00, // Pourcentage de financement
            'K' => NumberFormat::FORMAT_CURRENCY_EUR,  // Contributions TTC
            'L' => NumberFormat::FORMAT_CURRENCY_EUR,  // Contributions HT
            'M' => NumberFormat::FORMAT_CURRENCY_EUR,  // Coût global TTC
            'N' => NumberFormat::FORMAT_CURRENCY_EUR,  // Coût global HT
            'O' => NumberFormat::FORMAT_CURRENCY_EUR,  // Prix crédit carbone
        ];
    }

    public function title(): string
    {
        return 'Projets';
    }

    public function sheets(): array
    {
        $sheets = [];

        // 0) Index
        $sheets['Index'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle {
            protected $rows;
            public function __construct()
            {
                $this->rows = collect([
                    ['Onglet', 'Description'],
                    ['Projets', 'Vue synthétique des projets (totaux, ratios, états).'],
                    ['Sous-projets', 'Sous-projets avec les mêmes colonnes que Projets.'],
                    ['Dons', 'Contributions brutes (lien et source).'],
                    ['Financeurs', 'Informations financeurs + contacts (3 max).'],
                    ['Affectations', 'Détail des contributions affectées (splits).'],
                    ['Prix carbone', 'Historique des prix carbone par projet.'],
                    ['Partenaires', 'Paramétrage des partenaires et commissions.'],
                    ['Paiements partenaires', 'Paiements versés aux partenaires.'],
                    ['Paiements porteur', 'Paiements versés aux porteurs de projet.'],
                    ['Contrôle', 'Vérifications: totaux, écarts, dons non fléchés.'],
                ]);
            }
            public function collection() { return $this->rows; }
            public function headings(): array { return []; }
            public function title(): string { return 'Index'; }
        };

        // 1) Projets
        $sheets['Projets'] = $this;

        // 2) Sous-projets
        $sheets['Sous-projets'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() {
                return Project::with(['tenant','donationSplits','certification','sponsor','segmentation','referent','parentProject','methodForm','activeCarbonPrice'])
                    ->whereNotNull('parent_project_id')->get();
            }
            public function headings(): array {
                return [
                    'ID sous-projet','Nom','Projet parent','Statut principal','Méthode','Statut méthode','Label','Segmentation',
                    'Objectif tCO2','Contributions tCO2','Pourcentage de financement',
                    'Contributions TTC €','Contributions HT €','Coût global TTC','Coût global HT','Prix crédit carbone HT plateforme (actuel)',
                    'Durée du projet (années)','Adresse','Code postal','Ville',
                    'Type de porteur','Porteur','SIRET porteur','Mail du porteur','Référent','Mail du référent','Instance locale',
                ];
            }
            public function map($row): array {
                $contribTtc = (float) $row->donationSplits->sum('amount');
                $contribHt = TVAHelper::getHT($contribTtc);
                $costHt = TVAHelper::getHT((float) $row->cost_global_ttc);
                $financingRatio = ((float) $row->cost_global_ttc > 0) ? ($contribTtc / (float) $row->cost_global_ttc) : 0.0;
                $sponsorType = match ($row->sponsor ? get_class($row->sponsor) : null) {
                    Organization::class => 'Organisation',
                    User::class => 'Particulier',
                    default => 'Inconnu',
                };
                $sponsorName = match ($row->sponsor ? get_class($row->sponsor) : null) {
                    Organization::class, User::class => $row->sponsor?->name ?? '',
                    default => 'Inconnu',
                };
                $sponsorEmail = match ($row->sponsor ? get_class($row->sponsor) : null) {
                    Organization::class => $row->sponsor?->billing_email ?? '',
                    User::class => $row->sponsor?->email ?? '',
                    default => 'Inconnu',
                };
                return [
                    $row->id,
                    $row->name,
                    $row->parentProject?->name,
                    $row->state->humanName(),
                    $row->methodForm?->name ?? '',
                    $row->certification_state->humanName(),
                    $row->certification?->name ?? '',
                    $row->segmentation?->name ?? '',
                    $row->tco2,
                    $row->donationSplits->sum('tonne_co2'),
                    $financingRatio,
                    $contribTtc,
                    $contribHt,
                    $row->cost_global_ttc,
                    $costHt,
                    $row->activeCarbonPrice?->price,
                    $row->cost_duration_years,
                    $row->address_1,
                    $row->address_postal_code,
                    $row->address_city,
                    $sponsorType,
                    $sponsorName,
                    ($row->sponsor instanceof Organization) ? $row->sponsor?->siret : '',
                    $sponsorEmail,
                    $row->referent?->name ?? '',
                    $row->referent?->email ?? '',
                    $row->tenant?->name,
                ];
            }
            public function columnFormats(): array { return [
                'K' => NumberFormat::FORMAT_PERCENTAGE_00,
                'L' => NumberFormat::FORMAT_CURRENCY_EUR,
                'M' => NumberFormat::FORMAT_CURRENCY_EUR,
                'N' => NumberFormat::FORMAT_CURRENCY_EUR,
                'O' => NumberFormat::FORMAT_CURRENCY_EUR,
                'P' => NumberFormat::FORMAT_CURRENCY_EUR,
            ]; }
            public function title(): string { return 'Sous-projets'; }
        };

        // 3) Dons
        $sheets['Dons'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return Donation::query()->with('related')->get(); }
            public function headings(): array { return ['ID don','Type financeur','ID financeur','Montant TTC','Créé le','Mis à jour le','Lien','Informations source']; }
            public function map($row): array {
                $base = rtrim(config('app.url', 'http://localhost:8005'), '/');
                return [ $row->id, $row->related_type, $row->related_id, $row->amount, $row->created_at, $row->updated_at, $base.'/contributions/'.$row->id.'/split', $row->source_informations ? json_encode($row->source_informations, JSON_UNESCAPED_UNICODE) : null ];
            }
            public function columnFormats(): array { return [ 'D' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Dons'; }
        };

        // 4) Financeurs
        $sheets['Financeurs'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return Donation::query()->with('related')->get(); }
            public function headings(): array {
                return [
                    'ID don','Type financeur','ID financeur',
                    'Nom de l’organisation','Total contributions TTC €','Total contributions HT €','Email de facturation',
                    'SIRET','SIREN','Raison sociale','Code activité','Date de création','Sociétaire','Représentants',
                    'Email (générique)','Téléphone',
                    'Représentant 1 - Nom','Représentant 1 - Prénom','Représentant 1 - Email','Représentant 1 - Téléphone',
                    'Représentant 2 - Nom','Représentant 2 - Prénom','Représentant 2 - Email','Représentant 2 - Téléphone',
                    'Représentant 3 - Nom','Représentant 3 - Prénom','Représentant 3 - Email','Représentant 3 - Téléphone',
                    'Représentant 4 - Nom','Représentant 4 - Prénom','Représentant 4 - Email','Représentant 4 - Téléphone',
                    'Représentant 5 - Nom','Représentant 5 - Prénom','Représentant 5 - Email','Représentant 5 - Téléphone',
                    'Annuaire : Nom 1','Annuaire Email 1','Annuaire Téléphone 1','Annuaire Nom 2',' Annuaire Email 2',' Annuaire Téléphone 2',' Annuaire Nom 3','Annuaire Email 3','Annuaire Téléphone 3',
                ];
            }
            public function map($row): array {
                $name = null; $billingEmail = null; $siret = null; $siren = null; $legalName = null; $activity = null; $createdAt = null; $isSoc = null; $reps = null; $contactEmail = null; $contactPhone = null; $contacts = [];
                $representatives = [];
                $related = $row->related;
                if ($related) {
                    if ($related instanceof Organization) {
                        $name = $related->name;
                        $billingEmail = $related->billing_email ?? $related->contact_email ?? null;
                        $siret = $related->legal_siret ?? null;
                        $siren = $related->legal_siren ?? null;
                        $legalName = $related->legal_name ?? null;
                        $activity = $related->legal_activity_code ?? null;
                        $createdAt = $related->legal_created_at ?? null;
                        $isSoc = $related->is_shareholder ? 'Oui' : 'Non';
                        $reps = optional($related->users)->map(fn($u) => trim(($u->first_name ?? '').' '.($u->last_name ?? '')))->filter()->unique()->join(', ');
                        $representatives = optional($related->users)->map(function($u){
                            return [
                                'last_name' => $u->last_name ?? null,
                                'first_name' => $u->first_name ?? null,
                                'email' => $u->email ?? null,
                                'phone' => $u->phone ?? null,
                            ];
                        })->values()->all() ?? [];
                        $contactEmail = $related->contact_email ?? null;
                        $contactPhone = $related->contact_phone ?? null;
                        $contacts = is_array($related->contacts) ? array_values($related->contacts) : [];
                    } elseif ($related instanceof User) {
                        $name = $related->name;
                        $billingEmail = $related->email;
                    } else {
                        $name = $related->name ?? null;
                        $billingEmail = $related->email ?? null;
                    }
                }
                $totalTtc = null; $totalHt = null;
                if ($related) {
                    $donations = Donation::query()->where('related_type', get_class($related))->where('related_id', $related->id)->get(['amount']);
                    $totalTtc = (float) $donations->sum('amount');
                    $totalHt = TVAHelper::getHT($totalTtc);
                }
                $repFlat = [];
                for ($i = 0; $i < 5; $i++) {
                    $r = $representatives[$i] ?? ['last_name'=>null,'first_name'=>null,'email'=>null,'phone'=>null];
                    $repFlat[] = $r['last_name'];
                    $repFlat[] = $r['first_name'];
                    $repFlat[] = $r['email'];
                    $repFlat[] = $r['phone'];
                }
                $flat = [];
                for ($i = 0; $i < 3; $i++) {
                    $c = $contacts[$i] ?? null; $fullName = null;
                    if ($c) {
                        $first = $c['first_name'] ?? ($c['prenom'] ?? $c['firstName'] ?? null);
                        $last = $c['last_name'] ?? ($c['nom'] ?? $c['lastName'] ?? null);
                        $fullName = trim(trim((string)($first ?? '')) . ' ' . trim((string)($last ?? '')));
                        if ($fullName === '') { $fullName = $c['name'] ?? null; }
                    }
                    $flat[] = $fullName;
                    $flat[] = $c['email'] ?? null;
                    $flat[] = $c['phone'] ?? ($c['telephone'] ?? null) ?? null;
                }
                return array_merge([
                    $row->id,
                    $row->related_type,
                    $row->related_id,
                    $name,
                    $totalTtc,
                    $totalHt,
                    $billingEmail,
                    $siret,
                    $siren,
                    $legalName,
                    $activity,
                    $createdAt,
                    $isSoc,
                    $reps,
                    $contactEmail,
                    $contactPhone,
                ], $repFlat, $flat);
            }
            public function columnFormats(): array { return [ 'E' => NumberFormat::FORMAT_CURRENCY_EUR, 'F' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Financeurs'; }
        };

        // 5) Affectations (splits)
        $sheets['Affectations'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() {
                $items = DonationSplit::query()
                    ->with(['project', 'donation.related', 'projectCarbonPrice'])
                    ->withCount('childrenSplits')
                    ->orderBy('created_at')
                    ->get();
                return $items->filter(fn($it) => !is_null($it->donation_split_id) || ($it->children_splits_count == 0))->values();
            }
            public function headings(): array {
                return ['id','project_id','project_name','donation_id','donor_organization','project_carbon_price_id','project_carbon_price (HT)','project_carbon_price (TTC)','donation_split_id','Contributions € TTC','Contributions € HT','tonne_co2','created_at','updated_at'];
            }
            public function map($row): array {
                $projectName = $row->project->name ?? null;
                $donorOrg = null;
                if ($row->donation && $row->donation->related) {
                    $rel = $row->donation->related;
                    if ($rel instanceof Organization) { $donorOrg = $rel->name; }
                    elseif (property_exists($rel, 'name')) { $donorOrg = $rel->name; }
                }
                $priceHt = $row->projectCarbonPrice->price ?? null;
                $priceTtc = $priceHt !== null ? $priceHt * 1.2 : null;
                $amountTtc = (float) ($row->amount ?? 0);
                $amountHt = TVAHelper::getHT($amountTtc);
                return [
                    $row->id,
                    $row->project_id,
                    $projectName,
                    $row->donation_id,
                    $donorOrg,
                    $row->project_carbon_price_id,
                    $priceHt,
                    $priceTtc,
                    $row->donation_split_id,
                    $amountTtc,
                    $amountHt,
                    $row->tonne_co2,
                    $row->created_at,
                    $row->updated_at,
                ];
            }
            public function columnFormats(): array { return [ 'G' => NumberFormat::FORMAT_CURRENCY_EUR, 'H' => NumberFormat::FORMAT_CURRENCY_EUR, 'J' => NumberFormat::FORMAT_CURRENCY_EUR, 'K' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Affectations'; }
        };

        // 6) Prix carbone
        $sheets['Prix carbone'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return \App\Models\ProjectCarbonPrice::query()->with('project')->get(); }
            public function headings(): array { return ['id','project_id','project_name','Prix (HT)','Prix (TTC)','start_at','end_at','created_at','updated_at']; }
            public function map($row): array {
                $priceHt = $row->price; $priceTtc = is_null($priceHt) ? null : $priceHt * 1.2;
                return [ $row->id, $row->project_id, $row->project->name ?? null, $priceHt, $priceTtc, $row->start_at, $row->end_at, $row->created_at, $row->updated_at ];
            }
            public function columnFormats(): array { return [ 'D' => NumberFormat::FORMAT_CURRENCY_EUR, 'E' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Prix carbone'; }
        };

        // 7) Partenaires
        $sheets['Partenaires'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return \App\Models\PartnerProject::query()->with('partner')->get(); }
            public function headings(): array { return ['id','project_id','partner_id','partner_name','commission_type','commission_percentage','commission_numerical (TTC)','created_at','updated_at']; }
            public function map($row): array {
                $commissionType = $row->commission_type; if ($commissionType) { $commissionType = method_exists($commissionType,'databaseKey') ? $commissionType->databaseKey() : (property_exists($commissionType,'value')?$commissionType->value:(string)$commissionType); }
                $percentage = is_null($row->commission_percentage) ? null : ((float)$row->commission_percentage)/100.0;
                return [ $row->id, $row->project_id, $row->partner_id, $row->partner->name ?? null, $commissionType, $percentage, $row->commission_numerical, $row->created_at, $row->updated_at ];
            }
            public function columnFormats(): array { return [ 'F' => NumberFormat::FORMAT_PERCENTAGE_00, 'G' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Partenaires'; }
        };

        // 8) Paiements partenaires
        $sheets['Paiements partenaires'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return \App\Models\PartnerProjectPayment::query()->with('partnerProject.partner')->get(); }
            public function headings(): array { return ['id','partner_project_id','Nom partenaire','Montant TTC','Statut de paiement','created_at','updated_at']; }
            public function map($row): array {
                $name = optional($row->partnerProject?->partner)->name;
                $state = $row->payment_state; if($state){ $state = method_exists($state,'databaseKey')?$state->databaseKey():(property_exists($state,'value')?$state->value:(string)$state); }
                return [ $row->id, $row->partner_project_id, $name, $row->amount, $state, $row->created_at, $row->updated_at ];
            }
            public function columnFormats(): array { return [ 'D' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Paiements partenaires'; }
        };

        // 9) Paiements porteur
        $sheets['Paiements porteur'] = new class implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle, WithColumnFormatting {
            public function collection() { return \App\Models\ProjectHolderPayment::query()->with('project.sponsor')->get(); }
            public function headings(): array { return ['id','project_id','Nom porteur','Montant HT','Montant TTC','created_at','updated_at']; }
            public function map($row): array {
                $project = $row->project; $name = null; if ($project && $project->sponsor) { $name = $project->sponsor->name ?? null; }
                return [ $row->id, $row->project_id, $name, $row->amount_ht, $row->amount, $row->created_at, $row->updated_at ];
            }
            public function columnFormats(): array { return [ 'D' => NumberFormat::FORMAT_CURRENCY_EUR, 'E' => NumberFormat::FORMAT_CURRENCY_EUR ]; }
            public function title(): string { return 'Paiements porteur'; }
        };

        // 10) Contrôle
        $sheets['Contrôle'] = new class implements FromCollection, ShouldAutoSize, WithTitle {
            public function collection() {
                $rows = [];
                $totalDonationsTtc = Donation::query()->sum('amount');
                $totalFlecheParents = DonationSplit::query()->whereNull('donation_split_id')->sum('amount');
                $splits = DonationSplit::query()->withCount('childrenSplits')->get();
                $filteredSplits = $splits->filter(fn($it) => !is_null($it->donation_split_id) || ($it->children_splits_count == 0));
                $totalSplitsFiltered = (float) $filteredSplits->sum('amount');

                $rows[] = ['Totaux', 'Montant TTC (dons)', $totalDonationsTtc];
                $rows[] = ['Totaux', 'Montant fléché (splits parents)', $totalFlecheParents];
                $rows[] = ['Totaux', 'Montant splits (filtrés enfant/priorité enfant)', $totalSplitsFiltered];
                $rows[] = ['Écart', 'TTC - Fléché', $totalDonationsTtc - $totalFlecheParents];
                $rows[] = ['Écart', 'Fléché - Splits filtrés', $totalFlecheParents - $totalSplitsFiltered];

                $rows[] = [''];
                $rows[] = ['Dons non/partiellement fléchés'];
                $rows[] = ['donation_id', 'montant_ttc', 'montant_fléché', 'reste_a_flécher', 'lien_contribution'];
                $donations = Donation::query()->get();
                foreach ($donations as $d) {
                    $fleche = (float) $d->donationSplits()->onlyParents()->sum('amount');
                    if ($fleche < (float) $d->amount) {
                        $base = rtrim(config('app.url', 'http://localhost:8005'), '/');
                        $rows[] = [ $d->id, (float) $d->amount, $fleche, (float) $d->amount - $fleche, $base . '/contributions/' . $d->id . '/split' ];
                    }
                }

                $rows[] = [''];
                $rows[] = ['Écarts parent/enfant (origine de "Fléché - Splits filtrés")'];
                $rows[] = ['parent_split_id','donation_id','parent_project_id','project_name','montant_parent','somme_enfants','diff_parent_moins_enfants','lien_contribution'];
                $parents = DonationSplit::query()->whereNull('donation_split_id')->with('childrenSplits')->get();
                foreach ($parents as $p) {
                    $sumChildren = (float) $p->childrenSplits->sum('amount');
                    $diff = (float) $p->amount - $sumChildren;
                    if (abs($diff) > 0.00001) {
                        $projectName = \App\Models\Project::find($p->project_id)?->name;
                        $base = rtrim(config('app.url', 'http://localhost:8005'), '/');
                        $rows[] = [ $p->id, $p->donation_id, $p->project_id, $projectName, (float) $p->amount, $sumChildren, $diff, $base . '/contributions/' . $p->donation_id . '/split' ];
                    }
                }

                return collect($rows);
            }
            public function title(): string { return 'Contrôle'; }
        };

        return $sheets;
    }
}
