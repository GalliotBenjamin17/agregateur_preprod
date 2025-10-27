<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \App\Models\Project::whereIn('state', [
            \App\Enums\Models\Projects\ProjectStateEnum::Submitted,
            \App\Enums\Models\Projects\ProjectStateEnum::Approved,
        ])->update([
            'state' => \App\Enums\Models\Projects\ProjectStateEnum::Pending,
        ]);

        \App\Models\Project::whereIn('state', [
            \App\Enums\Models\Projects\ProjectStateEnum::Done,
        ])->update([
            'state' => \App\Enums\Models\Projects\ProjectStateEnum::Archived,
        ]);
    }

    public function down(): void
    {
        // Pas de down actions
    }
};
