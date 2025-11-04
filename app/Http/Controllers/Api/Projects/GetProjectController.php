<?php

namespace App\Http\Controllers\Api\Projects;

use App\Enums\Models\News\NewsStateEnum;
use App\Helpers\TVAHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GetProjectController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->load([
            'tenant:id,name,logo,public_url,primary_color,primary_color_text',
            'certification:id,name,image,image_black_and_white',
            'sponsor',
            'referent:id,first_name,last_name', // Charger le référent
            'methodForm:id,name', // Charger la méthode
            'segmentation:id,name,description',
            'activeCarbonPrice',
            'sustainableDevelopmentGoals',
            'annualReport',
            'parentProject' => [
                'tenant:id,name,logo,public_url,primary_color,primary_color_text',
                'certification:id,name,image,image_black_and_white',
                'sponsor',
                'segmentation:id,name,description',
                'activeCarbonPrice',
                'sustainableDevelopmentGoals',
                'annualReport',
            ],
            'news' => function ($query) {
                return $query->where('state', NewsStateEnum::Published)
                    ->with([
                        'author:id,first_name,last_name,email,tenant_id',
                    ]);
            },
            'annualReport',
        ]);

        $project->only([
            'id',
            'name',
            'slug',
            'summary',
            'description',
            'tenant_id',
            'certification_id',
            'thumbnail',
            'old_id',
            'featured_image',
            'address_1',
            'address_2',
            'address_postal_code',
            'address_city',
            'lat',
            'lng',
            'goal_text',
            'cost_global_ttc',
            'tco2',
            'cost_duration_years',
            'amount_wanted_ttc',
            'amount_wanted',
            'can_be_displayed_on_website',
            'can_be_financed_online',
            'can_be_displayed_percentage_of_funding',
            'can_be_displayed_on_terminal',
            'sponsor_type',
            'sponsor_id',
            'referent_id',
            'auditor_id',
            'segmentation_id',
            'created_at',
            'updated_at',
            'state',
            'certification_state',
            'is_funded',
            'annual_report_file_id',
            'parent_project_id',
            'method_form_id'
        ]);

        $isChildrenProject = $project->hasParent();

        // Manage featured_image: prioritize project image, otherwise inherit from parent if sub-project
        $final_featured_image_path = $project->featured_image;
        if ((is_null($final_featured_image_path) || $final_featured_image_path === '' || $final_featured_image_path === '/storage/') &&
            $isChildrenProject && $project->parentProject &&
            !is_null($project->parentProject->featured_image) && $project->parentProject->featured_image !== '' && $project->parentProject->featured_image !== '/storage/') {
            $final_featured_image_path = $project->parentProject->featured_image;
        }
        $project->featured_image = ($final_featured_image_path && $final_featured_image_path !== '/storage/') ? asset($final_featured_image_path) : null;

        // Manage thumbnail: prioritize project image, otherwise inherit from parent if sub-project
        $final_thumbnail_path = $project->thumbnail;
        if ((is_null($final_thumbnail_path) || $final_thumbnail_path === '' || $final_thumbnail_path === '/storage/') && $isChildrenProject && $project->parentProject && !is_null($project->parentProject->thumbnail) && $project->parentProject->thumbnail !== '' && $project->parentProject->thumbnail !== '/storage/') {
            $final_thumbnail_path = $project->parentProject->thumbnail;
        }
        $project->thumbnail = ($final_thumbnail_path && $final_thumbnail_path !== '/storage/') ? asset($final_thumbnail_path) : null;

        // Segmentation inheritance
        if ($isChildrenProject) {
            if ($project->parentProject) {
                // A child project's segmentation is determined by its parent.
                $project->setRelation('segmentation', $project->parentProject->segmentation);
                $project->segmentation_id = $project->parentProject->segmentation_id;
            } else {
                // This case implies hasParent() was true but parentProject isn't loaded (should not happen with current eager loads)
                // or parentProject is null. As a fallback, clear segmentation for the child.
                $project->setRelation('segmentation', null);
                $project->segmentation_id = null;
            }
        }

        $project->activeCarbonPrice['price_ttc'] = TVAHelper::getTTC($project->activeCarbonPrice->price);

        $project->tenant['logo'] = asset($project->tenant['logo']);

        if ($project->certification or ($isChildrenProject and $project->parentProject?->certification)) {
            $certificationParentProject = $project->parentProject?->certification()->first();

            $certification = $certificationParentProject ?? $project->certification;

            if (!$certification and $isChildrenProject) {
                $certification = $certificationParentProject;
            }


            $certification['image'] = match ($isChildrenProject) {
                true => ($certificationParentProject['image'] and $certificationParentProject['image'] != "/storage/") ? asset($certificationParentProject['image']) : null,
                false => ($project->certification['image'] and $project->certification['image'] != "/storage/") ? asset($project->certification['image']) : null
            };
            $certification['image_black_and_white'] = match ($isChildrenProject) {
                true => ($certificationParentProject['image_black_and_white'] and $certificationParentProject['image_black_and_white'] != "/storage/") ? asset($certificationParentProject['image_black_and_white']) : null,
                false => ($project->certification['image_black_and_white'] and $project->certification['image_black_and_white'] != "/storage/" ) ? asset($project->certification['image_black_and_white']) : null
            };

            unset($project['certification']);

            $project['certification'] = $certification->toArray();
        }

        if ($project->sponsor) {
            $project->sponsor['avatar'] = match ($isChildrenProject) {
                true => ($project->parentProject->sponsor['avatar'] and $project->parentProject->sponsor['avatar'] != "/storage/") ? asset($project->parentProject->sponsor['avatar']) : asset('img/empty/avatar.svg'),
                false => ($project->sponsor['avatar'] and $project->sponsor['avatar'] != "/storage/" ) ? asset($project->sponsor['avatar']) : asset('img/empty/avatar.svg')
            };
            $project->sponsor['phone'] = null;
            $project->sponsor['email'] = null;
            $project->sponsor["date_of_birth"] = null;
            $project->sponsor["gender"] = null;
            $project->sponsor["address_1"] = null;
            $project->sponsor["address_2"] = null;
            $project->sponsor["address_postal_code"] = null;
            $project->sponsor["address_city"] = null;
            $project->sponsor["welcome_valid_until"] = null;
            $project->sponsor["gdpr_consented_at"] = null;
            $project->sponsor["imported_by"] = null;
            $project->sponsor["tenant_id"] = null;
            $project->sponsor["is_shareholder"] = null;
            $project->sponsor["iban"] = null;
            $project->sponsor["bic"] = null;
            $project->sponsor["can_be_notified_transactional"] = null;
            $project->sponsor["can_be_notified_marketing"] = null;
            $project->sponsor["old_id"] = null;
            $project->sponsor["created_at"] = null;
            $project->sponsor["updated_at"] = null;

        }

        $project['odd'] = match ($isChildrenProject) {
            true => collect($project->parentProject['sustainableDevelopmentGoals'])->each(function ($goal) {
                $goal['image'] = asset($goal['image']);
            }),
            false => collect($project['sustainableDevelopmentGoals'])->each(function ($goal) {
                $goal['image'] = asset($goal['image']);
            })
        };

        $project['goal_text'] = ($isChildrenProject && empty($project->goal_text)) ? $project->parentProject?->goal_text : $project->goal_text;

        $project['progress_percentage'] = $project->cost_global_ttc > 0 ? round(($project->donationSplits->sum('amount') / $project->cost_global_ttc) * 100) : 0;

        $project['annual_report_file'] = $project->annualReport?->path ? asset('/storage/'.$project->annualReport->path) : null;

        $project->tco2 = match ($project->hasChildrenProjects()) {
            true => match ((bool) $project->is_goal_tco2_edited_manually) {
                true => $project->tco2,
                false => $project->childrenProjects->sum('tco2')
            },
            false => $project->tco2
        };

        $individual = $project->donationSplits()->whereRelation('donation', 'related_type', '=', User::class)->get()->pluck('donation.related_id')->unique()->count();
        $organization = $project->donationSplits()->whereRelation('donation', 'related_type', '=', Organization::class)->get()->pluck('donation.related_id')->unique()->count();

        $individualSum = $project->donationSplits()->whereRelation('donation', 'related_type', '=', User::class)->sum('tonne_co2');
        $organizationSum = $project->donationSplits()->whereRelation('donation', 'related_type', '=', Organization::class)->sum('tonne_co2');

        $individualSumEuros = $project->donationSplits()->whereRelation('donation', 'related_type', '=', User::class)->sum('amount');
        $organizationSumEuros = $project->donationSplits()->whereRelation('donation', 'related_type', '=', Organization::class)->sum('amount');

        $project['chart'] = [
            'donations_count' => $project->donationSplits()->count(),
            'previous_year_tons' => $project->donationSplits()->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])->sum('tonne_co2'),
            'split_count' => [
                'individual' => $individual,
                'organization' => $organization,
                'individual_percent' => $individual + $organization > 0 ? round(($individual / ($individual + $organization)) * 100, 2) : 0,
                'organization_percent' => $individual + $organization > 0 ? round(($organization / ($individual + $organization)) * 100, 2) : 0,
            ],
            'split_sum_tons' => [
                'individual' => $individualSum,
                'organization' => $organizationSum,
                'individual_percent' => $individual + $organization > 0 ? round(($individualSum / ($individualSum + $organizationSum)) * 100, 2) : 0,
                'organization_percent' => $individual + $organization > 0 ? round(($organizationSum / ($individualSum + $organizationSum)) * 100, 2) : 0,
            ],
            'split_sum_euros' => [
                'individual' => $individualSumEuros,
                'organization' => $organizationSumEuros,
                'individual_percent' => $individual + $organization > 0 ? round(($individualSumEuros / ($individualSumEuros + $organizationSumEuros)) * 100, 2) : 0,
                'organization_percent' => $individual + $organization > 0 ? round(($organizationSumEuros / ($individualSumEuros + $organizationSumEuros)) * 100, 2) : 0,
            ],
        ];

        if (! Arr::get($project, 'address_1') and $isChildrenProject) {
            $project['lat'] = $project->parentProject->lat;
            $project['lng'] = $project->parentProject->lng;
            $project['address_1'] = $project->parentProject->address_1;
            $project['address_2'] = $project->parentProject->address_2;
            $project['address_postal_code'] = $project->parentProject->address_postal_code;
            $project['address_city'] = $project->parentProject->address_city;
        }

        unset($project['childrenProjects']);
        unset($project['sustainableDevelopmentGoals']);
        unset($project['method_replies']);
        unset($project['donationSplits']);
        unset($project['parentProject']);

        // If it's a sub-project and its parent is certified, then the sub-project is also certified.
        // Otherwise, it depends on its own certification_state.
        if ($isChildrenProject && $project->parentProject && ($project->parentProject->certification_state->rank() >= 4)) {
            $project['is_certified'] = true;
        } else {
            $project['is_certified'] = $project->certification_state->rank() >= 4;
        }

        // Ajouter les nouveaux champs demandés
        $project->referent_name = $project->referent ? trim($project->referent->first_name . ' ' . $project->referent->last_name) : null;
        $project->method_name = $project->methodForm ? $project->methodForm->name : null;
        $project->certification_status_value = $project->certification_state ? $project->certification_state->value : null;

        return $project;
    }
}
