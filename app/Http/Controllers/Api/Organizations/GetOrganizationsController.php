<?php

namespace App\Http\Controllers\Api\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class GetOrganizationsController extends Controller
{
    /*
     * Get all organization on platform
     *
     * @param string tenant : [id] ; only for admin
     * @param bool count : [true, false, 1, 0]
     *
     */
    public function __invoke(Request $request)
    {
        //Load on conditions
        $organizationsQuery = Organization::query()
            ->when($request->has('tenant'), function ($query) use ($request) {
                return $query->where('tenant_id', $request->get('tenant'));
            })
            ->where('can_be_displayed_on_website', true);

        if ($request->boolean('count')) {
            return $organizationsQuery->count();
        }

        return $organizationsQuery->get()->each(function (Organization $item) {
            $item->avatar = asset($item->avatar);
        });
    }
}
