<?php

namespace App\Http\Controllers\Api\Projects;

use App\Helpers\TVAHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GetProjectsController extends Controller
{
    /*
     * Get all projects on platform
     *
     * @param bool visibleWebsite : [true, false, 1, 0]
     * @param bool visibleTerminal : [true, false, 1, 0]
     * @param string tenant : [id] ; only for admin
     * @param bool active : [true, false, 1, 0]
     * @param bool count : [true, false, 1, 0]
     *
     */
    public function __invoke(Request $request)
    {
        //Load on conditions
        $projectsQuery = Project::query()
            ->when($request->has('visibleWebsite'), function ($query) use ($request) {
                return $query->where('can_be_displayed_on_website', $request->boolean('visibleWebsite'));
            }, function ($query) {
                return $query->where('can_be_displayed_on_website', true);
            })
            ->when($request->boolean('visibleTerminal'), function ($query) use ($request) {
                return $query->where('can_be_displayed_on_terminal', $request->boolean('visibleTerminal'))
                    ->where('is_funded', false);
            })
            ->when($request->has('tenant'), function ($query) use ($request) {
                return $query->where('tenant_id', $request->get('tenant'));
            })
            ->when($request->boolean('active'), function ($query) {
                return $query->whereIn('state', [
                    'approved',
                    'pending',
                    'done',
                ]);
            });

        $projectsQuery = $projectsQuery->select([
            'id',
            'name',
            'slug',
            'summary',
            'old_id',
            'description',
            'tenant_id',
            'certification_id',
            'thumbnail',
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
            'is_goal_tco2_edited_manually',
            'state',
            'certification_state',
            'is_funded',
            'annual_report_file_id',
            'parent_project_id',
            'method_form_id'
        ]);

        // Load relationships
        $projectsQuery = $projectsQuery->with([
            'tenant:id,name,logo,public_url,primary_color,primary_color_text',
            'certification:id,name,image,image_black_and_white',
            'referent:id,first_name,last_name', // Load referent information
            'methodForm:id,name', // Load methodForm (methodology) information
            'sponsor',
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
            ]
        ]);

        if ($request->boolean('count')) {
            return $projectsQuery->count();
        }

        return $projectsQuery->get()->each(function (Project $item) {

            $isChildrenProject = $item->hasParent();

            // Manage featured_image: prioritize project image, otherwise inherit from parent if sub-project
            $final_featured_image_path = $item->featured_image;
            if ((is_null($final_featured_image_path) || $final_featured_image_path === '' || $final_featured_image_path === '/storage/') &&
                $isChildrenProject && $item->parentProject &&
                !is_null($item->parentProject->featured_image) && $item->parentProject->featured_image !== '' && $item->parentProject->featured_image !== '/storage/') {
                $final_featured_image_path = $item->parentProject->featured_image;
            }
            $item->featured_image = ($final_featured_image_path && $final_featured_image_path !== '/storage/') ? asset($final_featured_image_path) : null;

            // Manage thumbnail: prioritize project image, otherwise inherit from parent if sub-project
            $final_thumbnail_path = $item->thumbnail;
            if ((is_null($final_thumbnail_path) || $final_thumbnail_path === '' || $final_thumbnail_path === '/storage/') && $isChildrenProject && $item->parentProject && !is_null($item->parentProject->thumbnail) && $item->parentProject->thumbnail !== '' && $item->parentProject->thumbnail !== '/storage/') {
                $final_thumbnail_path = $item->parentProject->thumbnail;
            }
            $item->thumbnail = ($final_thumbnail_path && $final_thumbnail_path !== '/storage/') ? asset($final_thumbnail_path) : null;

            // Segmentation inheritance
            if ($isChildrenProject) {
                if ($item->parentProject) {
                    // A child project's segmentation is determined by its parent.
                    $item->setRelation('segmentation', $item->parentProject->segmentation);
                    $item->segmentation_id = $item->parentProject->segmentation_id;
                } else {
                    // This case implies hasParent() was true but parentProject isn't loaded (should not happen with current eager loads)
                    // or parentProject is null. As a fallback, clear segmentation for the child.
                    $item->setRelation('segmentation', null);
                    $item->segmentation_id = null;
                }
            }


            $item->activeCarbonPrice['price_ttc'] = TVAHelper::getTTC($item->activeCarbonPrice->price);

            $item->tenant['logo'] = asset($item->tenant['logo']);

            if ($item->certification or ($isChildrenProject and $item->parentProject?->certification)) {
                $certificationParentProject = $item->parentProject?->certification()->first();

                $certification = $certificationParentProject ?? $item->certification;

                if (!$certification and $isChildrenProject) {
                    $certification = $certificationParentProject;
                }


                $certification['image'] = match ($isChildrenProject) {
                    true => $certificationParentProject['image'] ? asset($certificationParentProject['image']) : null,
                    false => $item->certification['image'] ? asset($item->certification['image']) : null
                };
                $certification['image_black_and_white'] = match ($isChildrenProject) {
                    true => $certificationParentProject['image_black_and_white'] ? asset($certificationParentProject['image_black_and_white']) : null,
                    false => $item->certification['image_black_and_white'] ? asset($item->certification['image_black_and_white']) : null
                };

                unset($item['certification']);

                $item['certification'] = $certification->toArray();
            }

            if ($item->sponsor) {
                $item->sponsor['avatar'] = match ($isChildrenProject) {
                    true => ($item->parentProject->sponsor['avatar'] and $item->parentProject->sponsor['avatar'] != "/storage/") ? asset($item->parentProject->sponsor['avatar']) : asset('img/empty/avatar.svg'),
                    false => ($item->sponsor['avatar'] and $item->sponsor['avatar'] != "/storage/" ) ? asset($item->sponsor['avatar']) : asset('img/empty/avatar.svg')
                };

                $item->sponsor['contacts'] = null;
                $item->sponsor['phone'] = null;
                $item->sponsor['email'] = null;
                $item->sponsor["date_of_birth"] = null;
                $item->sponsor["gender"] = null;
                $item->sponsor["address_1"] = null;
                $item->sponsor["address_2"] = null;
                $item->sponsor["address_postal_code"] = null;
                $item->sponsor["address_city"] = null;
                $item->sponsor["welcome_valid_until"] = null;
                $item->sponsor["gdpr_consented_at"] = null;
                $item->sponsor["imported_by"] = null;
                $item->sponsor["tenant_id"] = null;
                $item->sponsor["is_shareholder"] = null;
                $item->sponsor["iban"] = null;
                $item->sponsor["bic"] = null;
                $item->sponsor["can_be_notified_transactional"] = null;
                $item->sponsor["can_be_notified_marketing"] = null;
                $item->sponsor["old_id"] = null;
                $item->sponsor["created_at"] = null;
                $item->sponsor["updated_at"] = null;

            }

            $item['odd'] = match ($isChildrenProject) {
                true => collect($item->parentProject['sustainableDevelopmentGoals'])->each(function ($goal) {
                    $goal['image'] = asset($goal['image']);
                }),
                false => collect($item['sustainableDevelopmentGoals'])->each(function ($goal) {
                    $goal['image'] = asset($goal['image']);
                })
            };

            $item['goal_text'] = ($isChildrenProject && empty($item->goal_text)) ? $item->parentProject?->goal_text : $item->goal_text;

            $item['progress_percentage'] = $item->cost_global_ttc > 0 ? round(($item->donationSplits->sum('amount') / $item->cost_global_ttc) * 100) : 0;

            $item['annual_report_file'] = $item->annualReport?->path ? asset('/storage/'.$item->annualReport->path) : null;

            $item->tco2 = match ($item->hasChildrenProjects()) {
                true => match ((bool) $item->is_goal_tco2_edited_manually) {
                    true => $item->tco2,
                    false => $item->childrenProjects->sum('tco2')
                },
                false => $item->tco2
            };

            $individual = $item->donationSplits()->with('donation')->whereRelation('donation', 'related_type', '=', User::class)->get()->pluck('donation.related_id')->unique()->count();
            $organization = $item->donationSplits()->with('donation')->whereRelation('donation', 'related_type', '=', Organization::class)->get()->pluck('donation.related_id')->unique()->count();

            $individualSum = $item->donationSplits()->whereRelation('donation', 'related_type', '=', User::class)->sum('tonne_co2');
            $organizationSum = $item->donationSplits()->whereRelation('donation', 'related_type', '=', Organization::class)->sum('tonne_co2');

            $individualSumEuros = $item->donationSplits()->whereRelation('donation', 'related_type', '=', User::class)->sum('amount');
            $organizationSumEuros = $item->donationSplits()->whereRelation('donation', 'related_type', '=', Organization::class)->sum('amount');

            $item['chart'] = [
                'donations_count' => $item->donationSplits()->count(),
                'previous_year_tons' => $item->donationSplits()->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])->sum('tonne_co2'),
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

            if (! Arr::get($item, 'address_1') and $isChildrenProject) {
                $item['lat'] = $item->parentProject->lat;
                $item['lng'] = $item->parentProject->lng;
                $item['address_1'] = $item->parentProject->address_1;
                $item['address_2'] = $item->parentProject->address_2;
                $item['address_postal_code'] = $item->parentProject->address_postal_code;
                $item['address_city'] = $item->parentProject->address_city;
            }

            unset($item['childrenProjects']);
            unset($item['sustainableDevelopmentGoals']);
            unset($item['method_replies']);
            unset($item['donationSplits']);
            unset($item['parentProject']);

            // If it's a sub-project and its parent is certified, then the sub-project is also certified.
            // Otherwise, it depends on its own certification_state.
            if ($isChildrenProject && $item->parentProject && ($item->parentProject->certification_state->rank() >= 4)) {
                $item['is_certified'] = true;
            } else {
                $item['is_certified'] = $item->certification_state->rank() >= 4;
            }

            // Add new fields
            $item['referent_name'] = $item->referent ? trim($item->referent->first_name . ' ' . $item->referent->last_name) : null;
            $item['method_name'] = $item->methodForm ? $item->methodForm->name : null;
            $item['certification_status_value'] = $item->certification_state ? $item->certification_state->value : null;
        });
    }
}
