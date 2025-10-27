<?php

namespace App\Http\Controllers\Gdpr;

use App\Http\Controllers\Controller;
use App\Models\GdprRequest;
use App\Notifications\Gdpr\SendCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StoreRequestController extends Controller
{
    const MINUTES_AFTER_EXPIRATION = 15;

    const SIZE_CODE = 12;

    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'key' => 'required|in:see,download,delete',
        ]);

        $executed = RateLimiter::attempt(
            'gdpr-ask:'.$request->ip(),
            $perMinute = 3,
            function () use ($request) {
                $gdprRequest = $this->handleDatabaseInfo($request);

                if ($gdprRequest->expires_at < now()) {
                    $gdprRequest->delete();
                    $gdprRequest = $this->handleDatabaseInfo($request);
                }

                Notification::route('mail', $gdprRequest->email)->notify(new SendCodeNotification(gdprRequest: $gdprRequest));

                Session::flash('success_gdpr', 'Votre demande a été enregistrée. Veuillez renseigner le code reçu par email dans le formulaire ci-dessous.');

                return true;
            }
        );

        if (! $executed) {
            Session::flash('alert', 'Vous ne pouvez pas faire une nouvelle demande de consultation de données.');

            return back();
        } else {
            return to_route('gdpr.hub.see.code');
        }
    }

    public function handleDatabaseInfo($request)
    {
        return GdprRequest::firstOrCreate([
            'email' => $request->input('email'),
            'type' => $request->input('key'),
        ],
            [
                'code' => Str::random(self::SIZE_CODE),
                'send_at' => now(),
                'expires_at' => now()->addMinutes(self::MINUTES_AFTER_EXPIRATION),
            ]);
    }
}
