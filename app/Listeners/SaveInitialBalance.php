<?php

namespace App\Listeners;

use App\Events\ItemCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\InitialBalance;
use Auth;
class SaveInitialBalance
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
     * @param  ItemCreated  $event
     * @return void
     */
    public function handle(ItemCreated $event)
    {
        // Log::info('item created', array('item', $event->item));
        $item = $event->item;
        $init_bal = new InitialBalance;
        $init_bal->item_id = $item->id;
        $init_bal->balance = $item->running_balance;
        $init_bal->encoder = Auth::user()->id;
        $init_bal->bal_remarks = $item->running_bal_remarks;
        $init_bal->save();
    }
}
