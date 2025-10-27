<?php

namespace App\View\Components;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class ReminderWidget extends Component
{
    public function __construct(
        public Model $model
    ) {
    }

    public function render(): View
    {
        return view('components.reminder-widget')->with([
            'reminders' => Reminder::where('related_id', $this->model->id)
                ->where('related_type', get_class($this->model))
                ->whereDate('reminder_at', '>=', Carbon::today()->startOfDay())
                ->orderBy('reminder_at')
                ->get(),
        ]);
    }
}
