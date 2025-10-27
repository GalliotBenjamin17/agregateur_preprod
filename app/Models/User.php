<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\Users\FirstLoginNotification;
use App\Notifications\Users\UpdatePasswordNotification;
use App\Traits\HasAddress;
use App\Traits\HasDonations;
use App\Traits\HasRedirection;
use App\Traits\HasSlug;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Soved\Laravel\Gdpr\Portable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\WelcomeNotification\ReceivesWelcomeNotification;

class User extends Authenticatable
{
    use AuthenticationLoggable, HasAddress, HasApiTokens, HasDonations, HasFactory, HasRedirection, HasRoles, HasSlug, HasUuids, Notifiable, Portable, ReceivesWelcomeNotification, Searchable;

    const DEFAULT_PASSWORD = '%wq3yKtlZ*7g0Z%MuQ!8&YHNgSfVHub1i2VF41rYx#pOuw3&b0';

    public $incrementing = false;

    protected $guarded = [
        'id',
        'slug',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'gdpr_consented_at' => 'datetime',
        'can_be_notified_transactional' => 'bool',
        'can_be_notified_marketing' => 'bool',
        'can_be_displayed_on_website' => 'bool',
        'is_shareholder' => 'boolean',
    ];

    public function toPortableArray()
    {
        $array = $this->toArray();

        return $array;
    }

    protected $gdprHidden = [
        'password',
        'remember_token',
    ];

    protected $gdprWith = [
        'comments',
        'files',
        'donations',
        'organizations',
    ];

    public function toSearchableArray()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->last_name = Str::upper($model->last_name);
            $model->email = Str::lower($model->email);
            $model->avatar = $model->avatar ?: '/img/empty/avatar.svg';
            $model->password = $model->password ?: self::DEFAULT_PASSWORD;
        });
    }

    public function name(): Attribute
    {
        return Attribute::get(function () {
            return $this->first_name.' '.$this->last_name;
        });
    }

    public function nameEmail(): Attribute
    {
        return Attribute::get(function () {
            return $this->first_name.' '.$this->last_name.' - '.$this->email;
        });
    }

    public function sendWelcomeNotification(Carbon $validUntil, bool $isMigration = false, bool $isRegister = false)
    {
        $this->notify(new FirstLoginNotification($validUntil, $isMigration, $isRegister));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new UpdatePasswordNotification(user: $this, token: $token));
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_organizations')
            ->withPivot(['organization_type_link_id', 'is_organization_manager']);
    }

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'partner_users');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function projectsAuditor(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_auditors');
    }

    public function projectsSponsor(): HasMany
    {
        return $this->hasMany(Project::class, 'sponsor_id', 'id')
            ->where('sponsor_type', get_class($this));
    }

    public function projectsReferent(): HasMany
    {
        return $this->hasMany(Project::class, 'referent_id', 'id');
    }

    public function donationSplits(): HasManyThrough
    {
        return $this->hasManyThrough(DonationSplit::class, Donation::class, 'related_id');
    }

    public function projectsCreated(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by', 'id');
    }

    public function projects()
    {
        return $this->projectsCreated()
            ->union($this->projectsSponsor()->toBase())
            ->union($this->projectsAuditor()->toBase())
            ->union($this->projectsReferent()->toBase());
    }

    public function scopeTenantable($query)
    {
        return $query->when(userHasTenant(), function ($query) {
            return $query->where('tenant_id', userTenantId());
        });
    }

    public function hasTenant(): bool
    {
        return (bool) $this->tenant_id;
    }
}
