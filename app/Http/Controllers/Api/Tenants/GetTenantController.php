<?php

namespace App\Http\Controllers\Api\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class GetTenantController extends Controller
{
    /*
     * Get tenant information
     *
     * @param string tenant : [id];
     *
     */
    public function __invoke(Request $request, Tenant $tenant)
    {

        $tenant = $tenant->only([
            'id',
            'name',
            'slug',
            'domain',
            'logo',
            'contributor_space_banner_activated',
            'contributor_space_banner_title',
            'contributor_space_banner_description',
            'contributor_space_banner_picture',
            'contributor_space_banner_button_text',
            'contributor_space_banner_button_url',
            'faq',
            'support_email',
            'dpo_email',
            'phone',
        ]);

        $tenant['logo'] = asset($tenant['logo']);
        $tenant['contributor_space_banner_picture'] = asset($tenant['contributor_space_banner_picture']);

        return $tenant;
    }
}
