<?php

namespace App\Providers;

use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\News;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerProjectPayment;
use App\Models\Project;
use App\Models\ProjectCarbonPrice;
use App\Models\ProjectHolderPayment;
use App\Models\User;
use App\Observers\DonationObserver;
use App\Observers\DonationSplitObserver;
use App\Observers\Models\ProjectHolderPaymentObserver;
use App\Observers\Models\UserObserver;
use App\Observers\NewsObserver;
use App\Observers\PartnerObserver;
use App\Observers\PartnerProjectObserver;
use App\Observers\PartnerProjectPaymentObserver;
use App\Observers\ProjectCarbonPriceObserver;
use App\Observers\ProjectObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        ProjectCarbonPrice::observe(ProjectCarbonPriceObserver::class);
        Donation::observe(DonationObserver::class);
        DonationSplit::observe(DonationSplitObserver::class);
        News::observe(NewsObserver::class);
        Partner::observe(PartnerObserver::class);
        PartnerProject::observe(PartnerProjectObserver::class);
        PartnerProjectPayment::observe(PartnerProjectPaymentObserver::class);
        ProjectHolderPayment::observe(ProjectHolderPaymentObserver::class);
        Project::observe(ProjectObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
