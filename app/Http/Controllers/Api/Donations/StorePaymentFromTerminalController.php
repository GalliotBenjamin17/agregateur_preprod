<?php

namespace App\Http\Controllers\Api\Donations;

use Akaunting\Setting\Support\Arr;
use App\Enums\Roles;
use App\Helpers\DonationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Donation\StoreDonationTerminalRequest;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\Terminal;
use App\Models\User;
use App\Services\Models\DonationService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorePaymentFromTerminalController extends Controller
{
    public function __invoke(StoreDonationTerminalRequest $request, Terminal $terminal, Project $project)
    {
        $tenant = $project->tenant;

        if (! $request->hasHeader('authorization')) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => [
                    'authorization' => [
                        'Missing `Authorization` header with tenant token id',
                    ],
                ],
            ]));
        }

        if ($request->header('authorization') != $tenant->auth_terminal_token) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => [
                    'authorization' => [
                        '`Authorization` doesn\'t match with tenant token id',
                    ],
                ],
            ]));
        }

        $terminal->load([
            'tenant:id,name,public_url,domain',
        ]);

        $amount = $request->get('paiement')['Montant'];
        $user = self::createUser(request: $request, tenant: $tenant);

        $donationService = new DonationService();
        $donation = $donationService->storeFromTerminal(
            tenant: $tenant,
            amount: $amount / 100,
            user: $user,
            sourceInformations: $request->all()
        );

        $splits = [
            [
                'type' => 'project',
                'data' => [
                    'project_id' => $project->id,
                    'amount' => $amount / 100,
                ],
            ],
        ];

        DonationHelper::buildSplit(donation: $donation, splits: $splits, splitBy: $user);

        return response()->json([
            'terminal' => $terminal,
            'project' => $project->only([
                'id', 'name', 'slug',
            ]),
            'donation' => $donation,
        ], 200);
    }

    public function createUser(Request $request, Tenant $tenant)
    {
        $email = $request->get('user')['email'];

        $user = User::where('email', $email)->first();

        // User already exist
        if ($user) {
            if (! $user->hasAnyRole([Roles::Admin, Roles::LocalAdmin]) and ! $user->hasAnyRole([Roles::Contributor])) {
                $user->assignRole(Roles::Contributor);
                $user->removeRole(Roles::Subscriber);
            }

            return $user;
        }

        // Need to create the user
        $names = self::extractNameFromEmail($email);

        $user = User::create([
            'email' => $email,
            'tenant_id' => $tenant->id,
            'first_name' => $names['first_name'],
            'last_name' => $names['last_name'],
            'phone' => $request->get('user')['telephone'],
            'password' => bcrypt(Str::random(30)),
            'gdpr_consented_at' => now(),
            'can_be_notified_marketing' => Arr::get($request->get('user'), 'AcceptEmailing', false),
            'can_be_notified_transactional' => Arr::get($request->get('user'), 'AcceptEmailing', false),
        ]);

        $user->assignRole(Roles::Contributor);
        $user->sendWelcomeNotification(validUntil: now()->addYear(), isMigration: false, isRegister: false);

        return $user;
    }

    public function extractNameFromEmail($email)
    {
        $name = [];

        // Extract the part before the @ symbol
        $localPart = strstr($email, '@', true);

        // Replace common separators with a space
        $localPart = preg_replace('/[._-]/', ' ', $localPart);

        // Remove any digits and special characters
        $localPart = preg_replace('/[^a-zA-Z\s]/', '', $localPart);

        // Convert the local part to title case (e.g., "john doe" -> "John Doe")
        $localPart = ucwords(strtolower($localPart));

        // Split the local part into words
        $words = array_filter(explode(' ', $localPart));

        // If there are at least two words, assume the first word is the first name and the last word is the last name
        if (count($words) >= 2) {
            $name['first_name'] = $words[0];
            $name['last_name'] = $words[count($words) - 1];
        } else {
            // If there is only one word, assume it is the first name and leave the last name empty
            $name['first_name'] = $localPart;
            $name['last_name'] = ' ';
        }

        return $name;
    }
}
