<?php

namespace Packages\Core;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Packages\Core\Events\EventLoginAdmin;
use Packages\Core\Listeners\CreateFolderLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EventLoginAdmin::class => [
            CreateFolderLogin::class
        ],
    ];
}
