<?php

namespace App\Http\Controllers\Gdpr;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Security\RgpdNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Soved\Laravel\Gdpr\Events\GdprDownloaded;

class AccountDownloadController extends Controller
{
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return $this->handle(Auth::user());
        }

        return Redirect::back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }

    private function handle(User $user): JsonResponse
    {
        $data = $user->portable();
        event(new GdprDownloaded($user));

        $now = Carbon::now()->toDateString();

        $user->notify(new RgpdNotification());

        return response()->json($data, 200,
            [
                'Content-Disposition' => "attachment; filename=\"user_$now.json\"",
            ]
        );
    }
}
