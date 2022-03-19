<?php

namespace App\Listeners;

use App\Events\ItemWasDelivered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Item;
class AddItemBalance
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ItemWasDelivered  $event
     * @return void
     */
    public function handle(ItemWasDelivered $event)
    {
       # to access 
       # use this 
       # $event->deliverItem
       $item = Item::findOrFail($event->deliverItem->item_id);
       $rsAdd = Item::where('id', $event->deliverItem->item_id)->increment('running_balance', $event->deliverItem->delivered_qty);
    }
}
