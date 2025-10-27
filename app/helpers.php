<?php

use App\Enums\Roles;
use Illuminate\Support\Str;

if (! function_exists('format')) {
    function format(?float $number, int $decimals = 2): ?string
    {
        if (! $number) {
            return $number;
        }

        return number_format($number, $decimals, ',', ' ');
    }
}

if (! function_exists('userHasTenant')) {
    function userHasTenant(): bool
    {
        return (bool) request()->user()?->tenant_id;
    }
}

if (! function_exists('userTenant')) {
    function userTenant(): ?\App\Models\Tenant
    {
        return request()->user()->tenant;
    }
}

if (! function_exists('userTenantId')) {
    function userTenantId(): ?string
    {
        return request()->user()->tenant_id;
    }
}

if (! function_exists('plural')) {
    function plural(string $choices, int $amount): string
    {
        return explode('|', $choices)[$amount > 1 ? 1 : 0];
    }
}

if (! function_exists('formatFileName')) {
    function formatFileName($name): string
    {
        return (string) Str::of($name)->replace(' ', '-')->lower();
    }
}

if (! function_exists('adminDashboardMiddleware')) {
    function adminDashboardMiddleware(): string
    {
        return 'role:'.Arr::join([Roles::Admin, Roles::LocalAdmin, Roles::Member, Roles::Auditor, Roles::Referent, Roles::Sponsor, Roles::Partner], '|', '|');
    }
}

if (! function_exists('urlFromS3')) {
    function urlFromS3(?string $path, ?int $minutes = null): ?string
    {
        if (is_null($path)) {
            return null;
        }

        if ($minutes) {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
        }

        return Storage::disk('s3')->url($path);
    }
}

if (! function_exists('storeToS3')) {
    function storeToS3($file, string $folder = 'root')
    {
        $originalName = $file->getClientOriginalName();

        $name = \Illuminate\Support\Str::of($originalName)
            ->beforeLast('.');

        $extension = \Illuminate\Support\Str::of($originalName)
            ->afterLast('.');

        return $file->storeAs($folder.'/', $name.'_'.strtolower(Str::random(3)).'.'.$extension, 's3');
    }
}

if (! function_exists('defaultSuccessNotification')) {
    function defaultSuccessNotification(?string $title = null, ?string $description = null): void
    {
        Filament\Notifications\Notification::make()
            ->title($title ?? 'Informations enregistrées')
            ->body($description)
            ->success()
            ->send();
    }
}
