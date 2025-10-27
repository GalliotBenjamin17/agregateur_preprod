<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $newRoles = [
            \App\Enums\Roles::Partner,
            \App\Enums\Roles::Subscriber,
        ];

        foreach ($newRoles as $role) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => $role,
            ], [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
    }
};
