<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sustainable_development_goals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('color');
            $table->string('image');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('project_sustainable_development_goals', function (Blueprint $table) {
            $table->uuid('project_id');
            $table->uuid('sustainable_development_goals_id');
        });

        $goals = [
            [
                'goal' => 1,
                'title' => 'End poverty in all its forms everywhere',
                'short' => 'Éradication de la pauvreté',
                'colorInfo' => [
                    'hex' => '#e5243b',
                    'rgb' => [
                        229,
                        36,
                        59,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'poverty',
                    ],
                    'descriptions' => [
                        'extreme poverty',
                        'basic standard of living',
                        'social protection',
                    ],
                    'groups' => [
                        'poorest',
                        'vulnerable',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_1.png',
            ],
            [
                'goal' => 2,
                'title' => 'End hunger, achieve food security and improved nutrition and promote sustainable agriculture',
                'short' => 'Lutte contre la faim',
                'colorInfo' => [
                    'hex' => '#e5b735',
                    'rgb' => [
                        229,
                        183,
                        53,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'hunger',
                        'food security',
                        'nutrition',
                        'sustainable agriculture',
                    ],
                    'descriptions' => [
                        'malnutrition',
                        'food production',
                        'agricultural productivity',
                        'agricultural investments',
                        'food markets',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_2.png',
            ],
            [
                'goal' => 3,
                'title' => 'Ensure healthy lives and promote well-being for all at all ages',
                'short' => 'Accès à la santé',
                'colorInfo' => [
                    'hex' => '#4c9f38',
                    'rgb' => [
                        76,
                        159,
                        56,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'health',
                        'well-being',
                    ],
                    'descriptions' => [
                        'reproductive health',
                        'maternal health',
                        'child health',
                        'epidemics',
                        'communicable diseases',
                        'non-communicable diseases',
                        'environmental diseases',
                        'universal health coverage',
                        'medicines',
                        'vaccines',
                    ],
                    'groups' => [
                        'women',
                        'children',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_3.png',
            ],
            [
                'goal' => 4,
                'title' => 'Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all',
                'short' => 'Accès à une éducation de qualité',
                'colorInfo' => [
                    'hex' => '#c5192d',
                    'rgb' => [
                        197,
                        25,
                        45,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'education',
                    ],
                    'descriptions' => [
                        'skills',
                        'technical education',
                        'vocational educaiton',
                        'training',
                        'higher education',
                        'knowledge',
                        'values',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_4.png',
            ],
            [
                'goal' => 5,
                'title' => 'Achieve gender equality and empower all women and girls',
                'short' => 'Égalité entre les sexes',
                'colorInfo' => [
                    'hex' => '#ff3a21',
                    'rgb' => [
                        255,
                        58,
                        33,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'gender',
                        'women',
                        'girls',
                    ],
                    'descriptions' => [
                        'women empowerment',
                        'girls empowerment',
                        'discrimination against women',
                        'violence against women',
                        'sexual health',
                        'reproductive health',
                        'reproductive rights',
                        'unpaid work',
                        'access to productive resources',
                        'equal participation',
                        'political life',
                        'economic life',
                        'public life',
                    ],
                    'groups' => [
                        'women',
                        'girls',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_5.png',
            ],
            [
                'goal' => 6,
                'title' => 'Ensure availability and sustainable management of water and sanitation for all',
                'short' => "Accès à l'eau salubre et à l'assainissement",
                'colorInfo' => [
                    'hex' => '#26bde2',
                    'rgb' => [
                        38,
                        189,
                        226,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'water',
                        'sanitation',
                    ],
                    'descriptions' => [
                        'drinking water',
                        'hygiene',
                        'water resources',
                        'water management',
                        'sanitation management',
                    ],
                    'groups' => [
                        'international cooperation',
                        'local communities',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_6.png',
            ],
            [
                'goal' => 7,
                'title' => 'Ensure access to affordable, reliable, sustainable and modern energy for all',
                'short' => 'Recours aux énergies renouvelables',
                'colorInfo' => [
                    'hex' => '#fcc30b',
                    'rgb' => [
                        252,
                        195,
                        11,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'energy',
                    ],
                    'descriptions' => [
                        'energy access',
                        'renewable energy',
                    ],
                    'groups' => [
                        'international cooperation',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_7.png',
            ],
            [
                'goal' => 8,
                'title' => 'Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all',
                'short' => 'Accès à des emplois décents',
                'colorInfo' => [
                    'hex' => '#a21942',
                    'rgb' => [
                        162,
                        25,
                        66,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'economic growth',
                        'employment',
                        'decent work',
                    ],
                    'descriptions' => [
                        'forced labour',
                        'human trafficking',
                        'child labour',
                    ],
                    'groups' => [
                        'children',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_8.png',
            ],
            [
                'goal' => 9,
                'title' => 'Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation',
                'short' => 'Industrie, innovation et infrastructures',
                'colorInfo' => [
                    'hex' => '#fd6925',
                    'rgb' => [
                        253,
                        105,
                        37,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'infrastructure',
                        'industrialization',
                        'innovation',
                    ],
                    'descriptions' => [
                        'research',
                        'technology',
                        'technical support',
                        'ICT',
                        'information and communication technology',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_9.png',
            ],
            [
                'goal' => 10,
                'title' => 'Reduce inequality within and among countries',
                'short' => 'Réduction des inégalités',
                'colorInfo' => [
                    'hex' => '#dd1367',
                    'rgb' => [
                        221,
                        19,
                        103,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'inequality',
                    ],
                    'descriptions' => [
                        'income',
                        'sex',
                        'age',
                        'disability',
                        'race',
                        'class',
                        'ethnicity',
                        'religion',
                        'migration',
                        'development assistance',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_10.png',
            ],
            [
                'goal' => 11,
                'title' => 'Make cities and human settlements inclusive, safe, resilient and sustainable',
                'short' => 'Villes et communautés durables',
                'colorInfo' => [
                    'hex' => '#fd9d24',
                    'rgb' => [
                        253,
                        157,
                        36,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'cities',
                        'human settlements',
                    ],
                    'descriptions' => [
                        'community cohesion',
                        'personal security',
                        'innovation',
                        'employment',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_11.png',
            ],
            [
                'goal' => 12,
                'title' => 'Ensure sustainable consumption and production patterns',
                'short' => 'Consommation et production responsables',
                'colorInfo' => [
                    'hex' => '#c9992d',
                    'rgb' => [
                        201,
                        153,
                        45,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'consumption',
                        'production',
                    ],
                    'descriptions' => [
                        'toxic materials',
                        'environment',
                    ],
                    'groups' => [
                        'international agreements',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_12.png',
            ],
            [
                'goal' => 13,
                'title' => 'Take urgent action to combat climate change and its impacts.',
                'short' => 'Lutte contre le changement climatique',
                'colorInfo' => [
                    'hex' => '#3f7e44',
                    'rgb' => [
                        63,
                        126,
                        68,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'climate change',
                    ],
                    'descriptions' => [
                        'resilience',
                        'natural disasters',
                    ],
                    'groups' => [
                        'poorest',
                        'vulnerable',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_13.png',
            ],
            [
                'goal' => 14,
                'title' => 'Conserve and sustainably use the oceans, seas and marine resources for sustainable development',
                'short' => 'Vie aquatique',
                'colorInfo' => [
                    'hex' => '#0a97d9',
                    'rgb' => [
                        10,
                        151,
                        217,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'oceans',
                        'seas',
                        'marine resources',
                    ],
                    'descriptions' => [
                        'marine ecosystems',
                        'coastal ecosystems',
                        'marine pollution',
                    ],
                    'groups' => [
                        'small islands developing States',
                        'SIDS',
                        'least developed countries',
                        'LDCs',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_14.png',
            ],
            [
                'goal' => 15,
                'title' => 'Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss',
                'short' => 'Vie terrestre',
                'colorInfo' => [
                    'hex' => '#56c02b',
                    'rgb' => [
                        86,
                        192,
                        43,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'terrestrial ecosystems',
                        'forests',
                        'desertification',
                        'land',
                        'biodiversity',
                    ],
                    'descriptions' => [
                        'degraded lands',
                        'forest management',
                        'natural habitats',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_15.png',
            ],
            [
                'goal' => 16,
                'title' => 'Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions at all levels',
                'short' => 'Justice et paix',
                'colorInfo' => [
                    'hex' => '#00689d',
                    'rgb' => [
                        0,
                        104,
                        157,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'peaceful societies',
                        'inclusive societies',
                        'justice',
                        'institutions',
                    ],
                    'descriptions' => [
                        'human rights',
                        'rule of law',
                        'violence',
                        'armed conflict',
                        'weak institutions',
                    ],
                    'groups' => [
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_16.png',
            ],
            [
                'goal' => 17,
                'title' => 'Strengthen the means of implementation and revitalize the global partnership for sustainable development',
                'short' => 'Partenariats pour la réalisation des objectifs',
                'colorInfo' => [
                    'hex' => '#19486a',
                    'rgb' => [
                        25,
                        72,
                        106,
                    ],
                ],
                'keywords' => [
                    'tags' => [
                        'means of implementation',
                        'partnerships',
                    ],
                    'descriptions' => [
                        'resource mobilization',
                    ],
                    'groups' => [
                        'developing countries',
                        'LDCs',
                    ],
                ],
                'icon_url' => 'https://raw.githubusercontent.com/UNStats-SDGs/sdgs-data/master/images/en/TGG_Icon_Color_17.png',
            ],
        ];

        foreach ($goals as $goal) {
            \App\Models\SustainableDevelopmentGoals::create([
                'name' => $goal['short'],
                'description' => $goal['title'],
                'color' => $goal['colorInfo']['hex'],
                'image' => '/img/odd/odd-'.sprintf("%'.02d", $goal['goal']).'.png',
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('sustainable_development_goals');
    }
};
