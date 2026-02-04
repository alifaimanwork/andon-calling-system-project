<?php

namespace App\Providers;

use App\Events\Opc\CountUpEvent;
use App\Events\Opc\OpcTagValueChangedEvent;
use App\Events\Terminal\CancelDieChangeEvent;
use App\Events\Terminal\CancelFirstProductConfirmationEvent;
use App\Events\Terminal\ProceedFirstProductConfirmationEvent;
use App\Events\Terminal\RejectSettingsUpdatedEvent;
use App\Events\Terminal\StartDieChangeEvent;
use App\Events\Terminal\TerminalUserLoginEvent;
use App\Events\Terminal\TerminalUserLogoutEvent;
use App\Events\Terminal\WorkCenterStateChangeEvent;
use App\Events\Web\WebUserLoginEvent;
use App\Events\Web\WebUserLogoutEvent;
use App\Listeners\LogActivity;
use App\Models\Company;
use App\Models\OpcServer;
use App\Models\Plant;
use App\Models\User;
use App\Observers\CompanyObserver;
use App\Observers\OpcServerObserver;
use App\Observers\PlantObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        //Web User Auth
        WebUserLoginEvent::class => [
            [LogActivity::class, 'handle']
        ],
        WebUserLogoutEvent::class => [
            [LogActivity::class, 'handle']
        ],

        //Terminal User Auth
        TerminalUserLoginEvent::class => [
            [LogActivity::class, 'handle']
        ],
        TerminalUserLogoutEvent::class => [
            [LogActivity::class, 'handle']
        ],

        //Workcenter
        WorkCenterStateChangeEvent::class => [],


        //Die-Change
        StartDieChangeEvent::class => [
            [LogActivity::class, 'handle']
        ],

        CancelDieChangeEvent::class => [
            [LogActivity::class, 'handle']
        ],
        ProceedFirstProductConfirmationEvent::class => [
            [LogActivity::class, 'handle']
        ],

        //First-Product-Confirmation
        RejectSettingsUpdatedEvent::class => [],
        CancelFirstProductConfirmationEvent::class => [
            [LogActivity::class, 'handle']
        ],

        //OPC Event
        OpcTagValueChangedEvent::class => [
            []
        ],
        CountUpEvent::class => []



    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        User::observe(UserObserver::class);
        Company::observe(CompanyObserver::class);
        Plant::observe(PlantObserver::class);
        OpcServer::observe(OpcServerObserver::class);
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
