<?php

namespace App\Http\Controllers\Gdpr;

use App\Http\Controllers\Controller;
use App\Models\GdprRequest;
use Illuminate\Http\Request;

class RedirectToCorrectPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $result = $this->searchCode($request->input('code'));

        $route = match ($result->type) {
            'see' => route('gdpr.hub.see.show', ['gdprRequest' => $result->id])
        };

        return redirect($route);
    }

    public function searchCode($code): ?GdprRequest
    {
        $gdprRequest = GdprRequest::select(['id', 'code', 'type'])->get();

        foreach ($gdprRequest as $request) {
            if ($request->code == $code) {
                return $request;
            }
        }

        return null;
    }
}
