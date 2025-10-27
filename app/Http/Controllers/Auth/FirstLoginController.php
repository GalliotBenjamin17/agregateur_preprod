<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tenant;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;
use Symfony\Component\HttpFoundation\Response;

class FirstLoginController extends BaseWelcomeController
{
    public string $protocol = 'https://';

    public function __construct()
    {
        if (config('app.env') == 'local') {
            $this->protocol = 'http://';
        }
    }

    public function showWelcomeForm(Request $request, User $user)
    {
        $tenant = Tenant::find($user->tenant_id);

        return view('welcomeNotification::welcome')->with([
            'email' => $request->email,
            'tenant' => $tenant,
            'user' => \App\Models\User::with('tenant')->where('id', $user->id)->first(),
            'logo' => setting('logo', 'img/logos/cooperative-carbone/logo_png.png'),
            'name' => setting('name', 'Coopérative Carbone'),
            'accentColor' => setting('brand_color', '#244999'),
            'textColor' => setting('text_color', '#ffffff'),
            'welcomeExplanations' => setting('welcome_explanations', 'Vous avez reçu un lien de configuration de compte pour vous connecter sur la plateforme de la Coopérative Carbone.'),
            'gdprExplanations' => setting('gdpr_explanations', "<p>Pour accéder à la plateforme, vous devez consentir à l'utilisation de vos données et aux traitements futurs de vos données personnelles.</p>"),
        ]);
    }

    public function savePassword(Request $request, User $user)
    {
        $request->validate($this->rules());

        $user->password = Hash::make($request->password);
        //$user->welcome_valid_until = null;
        //$user->gdpr_consented_at = now();
        $user->save();

        return $this->sendToRedirect($user);
    }

    public function rules()
    {
        return [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'conditions' => 'accepted',
        ];
    }

    public function sendToRedirect($user): Response
    {
        $tenant = Tenant::find($user->tenant_id);

        if (is_null($tenant)) {
            Auth::logout();

            return to_route('dashboard');
        }

        Auth::logout();

        Session::flash('success', 'Mot de passe mis à jour. Connectez-vous pour accéder au tableau de bord.');

        return redirect()->away($this->protocol.$tenant->domain.'.'.config('app.displayed_url'));
    }
}
