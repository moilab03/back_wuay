<?php

namespace App\Providers;

use App\GroupUserRoom;
use App\MessageRoom;
use App\Observers\GroupUserRoomObserver;
use App\Observers\MessageRoomObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
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
        MessageRoom::observe(MessageRoomObserver::class);
        GroupUserRoom::observe(GroupUserRoomObserver::class);
        parent::boot();
    }
}
