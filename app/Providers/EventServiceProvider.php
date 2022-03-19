<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\EventRequestItemCreated' => [
            'App\Listeners\DeductStockCardQuantity'
        ],
        'App\Events\ItemCreated' => [
            'App\Listeners\SaveInitialBalance'
        ],
        'App\Events\AprItemCreated' => [
            'App\Listeners\UpdateAprFormItems'
        ],
        'App\Events\ItemWasDelivered' => [
            'App\Listeners\AddItemBalance'
        ],
        'App\Events\PoItemWasCreatedInExistingPO' => [
            'App\Listeners\updatePurchaseOrderIncomplete'
        ],
        'App\Events\RisItemUpdatedorDeleted' => [
            'App\Listeners\updateRisItemList'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
