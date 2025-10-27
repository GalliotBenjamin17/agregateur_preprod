<?php

namespace App\Http\Controllers\Reminders;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class StoreReminderController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'reminder_at' => 'required|date|after_or_equal:today',
            'notification_at' => 'nullable|date|before_or_equal:reminder_at|after_or_equal:today',
            'related_type' => 'required',
            'related_id' => 'required',
        ], [
            'notification_at.before_or_equal' => 'La date de notification ne peut être après la date de rappel.',
        ]);

        $notificationAt = $request->input('notification_at') ? $request->date('notification_at') : $request->date('reminder_at');

        Reminder::create([
            'reminder_at' => $request->date('reminder_at'),
            'notification_at' => $notificationAt,
            'content' => $request->input('content'),
            'related_type' => $request->input('related_type'),
            'related_id' => $request->input('related_id'),
            'created_by' => $request->user()->id,
            'tenant_id' => $request->input('related_type')::where('id', $request->input('related_id'))->first()?->tenant_id ?? \Auth::user()->tenant_id,
        ]);

        return back();
    }
}
