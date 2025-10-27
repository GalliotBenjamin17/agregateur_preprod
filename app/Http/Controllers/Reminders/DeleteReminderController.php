<?php

namespace App\Http\Controllers\Reminders;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class DeleteReminderController extends Controller
{
    public function __invoke(Request $request, Reminder $reminder)
    {
        $reminder->delete();

        \Session::flash('success', 'Le rappel a été supprimé.');

        return back();
    }
}
