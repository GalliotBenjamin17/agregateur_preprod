<?php

namespace App\Http\Controllers\Terminals;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreTerminalController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'tenant_id' => 'required',
        ]);

        $terminal = Terminal::create($validated);

        Session::flash('success', 'La borne a été ajoutée.');

        return back();
    }
}
