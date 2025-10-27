<?php

namespace App\Http\Controllers\Api\Users;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GetUsersController extends Controller
{
    /*
     * Get all users on platform
     *
     * @param string tenant : [id] ; only for admin
     * @param bool active : [true, false, 1, 0]
     * @param bool count : [true, false, 1, 0]
     *
     */
    public function __invoke(Request $request)
    {
        //Load on conditions
        $usersQuery = User::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'slug',
                'avatar',
                'gender',
                'tenant_id',
                'is_shareholder',
                'can_be_displayed_on_website',
                'old_id',
                'updated_at',
            ])
            ->with([
                'roles',
            ])
            ->where('can_be_displayed_on_website', true)
            ->role([Roles::Contributor])
            ->has('donations')
            ->when($request->boolean('notifiable'), function ($query) use ($request) {
                return $query->where('can_be_notified_marketing', $request->boolean('notifiable'));
            })
            ->when($request->has('tenant'), function ($query) use ($request) {
                return $query->where('tenant_id', $request->get('tenant'));
            });

        // Load relationships
        $usersQuery = $usersQuery->with([
            'projectsReferent',
            'projectsAuditor',
            'projectsSponsor',
        ]);

        if ($request->boolean('count')) {
            return $usersQuery->count();
        }

        return $usersQuery->get()->each(function (User &$item) {
            $item->roles_simple = collect($item->roles)->pluck('name')->toArray();

            $item->avatar = asset($item->avatar);
            $item->is_auditor = $item->hasRole(Roles::Auditor);
            $item->is_projectholder = $item->hasRole(Roles::Sponsor);
            $item->is_contributor = $item->hasRole(Roles::Contributor);
            $item->is_referent = $item->hasRole(Roles::Referent);

        });
    }
}
