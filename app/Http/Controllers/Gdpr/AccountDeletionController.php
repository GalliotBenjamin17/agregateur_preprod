<?php

namespace App\Http\Controllers\Gdpr;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AccountDeletionController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $idDeletion = \Illuminate\Support\Str::uuid();

            $request->user()->update([
                'first_name' => 'Utilisateur',
                'last_name' => 'Supprimé',
                'slug' => $idDeletion,
                'email' => $idDeletion.'@trash.com',
                'password' => \Hash::make(\Illuminate\Support\Str::random(30)),
                'address_1' => null,
                'address_2' => null,
                'address_postal_code' => null,
                'address_city' => null,
                'iban' => null,
                'bic' => null,
                'can_be_displayed_on_website' => false,
                'can_be_notified_marketing' => false,
                'can_be_notified_transactional' => false,
                'old_id' => null,
                'avatar' => '/img/empty/avatar.svg',
            ]);

            return $this->handle();
        }

        return Redirect::back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }

    private function handle(): RedirectResponse
    {
        Auth::logout();
        Session::flash('success', 'Votre demande de suppression de compte a été prise en compte. Les données relatives à votre compte ont été anonymisées puis supprimées.');

        return Redirect::route('login');
    }
}
