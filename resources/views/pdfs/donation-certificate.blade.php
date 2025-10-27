<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de contribution - {{ $related->name }}</title>
    <style>
        html {
            margin: 0px
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header-img,
        .footer-img {
            position: fixed;
            left: 0px;
            right: 0px;
            width: 100%;
            z-index: -1000;
        }

        .header-img {
            top: 0;
            //height: 150px; /*  hauteur de image d'en-tête */
        }

        .main-container {
            width: 80%;
            margin: auto;
            margin-top: 170px; /* pour ne pas superposer l'en-tête */
            padding: 20px;
            text-align: center;
        }

        .title {
            color: #afc604;
            line-height: 12px;
            margin-bottom: 40px;
        }

        .info {
            text-align: left;
            margin-top: 20px;
            margin-bottom: 50px;
            line-height: 12px;
            font-size: 18px;
        }

        table {
            width: 100%;
            margin: auto;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #244999;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:nth-child(odd) {
            background-color: white;
        }

        .text-justify {
            text-align: justify !important;
            line-height: 20px;
            margin-top: 40px;
        }

        .signature {
            margin-top: 30px;
            line-height: 8px;
            text-align: left;
            padding-left: 55% !important;
        }

        .signature img {
            height: 80px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            height: 100px; /* hauteur de image de pied de page */
        }

        .footer-text {
            position: absolute;
            bottom: 10px;
            left: 20px;
            right: 20px;
            /*color: white;*/
            font-family: Arial, sans-serif;
            font-size: 9px;
            text-align: left;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <img class="header-img" src="{{ public_path('img/pdf/certificat/Header.png') }}">
    </div>

    <div class="main-container">
        <div class="title">
            <h2>CERTIFICAT DE CONTRIBUTION</h2>
            <h2>À LA NEUTRALITÉ CARBONE</h2>
        </div>

        <div class="info">
            <p><strong>N° :</strong> {{ $donation->id }}</p>
            @if (get_class($related) == \App\Models\Organization::class)
                <p style="line-height: 20px"><strong>Délivré à :</strong> {{ $related->name }}, {{ $related->address }}
                </p>
            @else
                <p><strong>Délivré à :</strong> {{ $related->name }}</p>
            @endif
        </div>

        <div class="info" style="margin-top: 0 !important;">
            <p>
                <strong>Contribution initiale :</strong> {{ format($donation->amount) }} € TTC
                ({{ format(\App\Helpers\TVAHelper::getHT($donation->amount)) }} € HT)
            </p>
            @if ($donationSplits->sum('amount') < $donation->amount)
                <p><strong>Disponible :</strong> {{ format($donation->amount - $donationSplits->sum('amount')) }} € </p>
            @endif
        </div>

        @if (sizeof($donationSplits) > 0)
            @php
                $header = [
                    'Nom du projet',
                    'Equivalent CO2 (t)',
                    'Prix tonne (€ TTC)',
                    'Segmentation',
                    'Méthode',
                    'Année audit (prév.)',
                ];
            @endphp
            <table>
                <thead>
                    <tr>
                        @foreach ($header as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donationSplits as $donationSplit)
                        @php
                            $project = $donationSplit->project;
                            $displayProject = $project->parentProject ?? $project;
                        @endphp
                        <tr>
                            <td>{{ $project->parentProject ? $project->parentProject->name . ' - ' . $project->name : $project->name }}, France</td>
                            <td>{{ $donationSplit->tonne_co2 }} @choice('tonne|tonnes', $donationSplit->tonne_co2)</td>
                            <td>{{ \App\Helpers\TVAHelper::getTTC(amount: $donationSplit->projectCarbonPrice->price) }}
                                €</td>
                            <td>{{ $displayProject->segmentation?->name ?? '-' }}</td>
                            <td>{{ $displayProject->methodForm?->methodFormGroup?->name ?? ($donationSplit->project->methodForm?->name ?? '-') }}
                            </td>
                            <td>{{ $project->getPlannedAuditYear() ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif


        @if (sizeof($donationSplits) > 5)
            <div class="page-break"></div>
            <div style="margin-top: 320px"></div>
        @endif

        @php
            use App\Enums\Models\Projects\CarbonCreditCharacteristicsEnum;

            $totalCo2 = $donationSplits->sum('tonne_co2');
            $sequestrationCo2 = $donationSplits
                ->filter(function ($split) {
                    return $split->project->credit_characteristics === CarbonCreditCharacteristicsEnum::Sequestration;
                })
                ->sum('tonne_co2');
            $avoidanceCo2 = $donationSplits
                ->filter(function ($split) {
                    return $split->project->credit_characteristics === CarbonCreditCharacteristicsEnum::Avoidance;
                })
                ->sum('tonne_co2');
        @endphp
        <div class="text-justify">
            <p><strong>Félicitations !</strong> Vous avez contribué à un projet local permettant d’adapter notre
                territoire au dérèglement climatique.</p>
            <p>Grâce à ce geste responsable, vous avez agi localement pour réduire votre impact sur le climat.</p>
        </div>

        <div class="signature">
            <p>Le {{ $donation->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
            <p>Anne Rostaing, Présidente du directoire</p>
            <img src="{{ public_path('img/pdf/certificat/image-001.png') }}">
        </div>
    </div>

    <div class="footer">
        <img class="footer-img" src="{{ public_path('img/pdf/certificat/Footer.png') }}">
        <div class="footer-text">
            Certificat généré par : {{ $tenant->name }}, SIRET : {{ $tenant->siret }}.
        </div>
    </div>

</body>

</html>
