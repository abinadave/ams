<?php

namespace App\Listeners;

use App\Events\PoItemWasCreatedInExistingPO;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use DB;

class updatePurchaseOrderIncomplete
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
     * @param  PoItemWasCreatedInExistingPO  $event
     * @return void
     */
    public function handle(PoItemWasCreatedInExistingPO $event)
    {
        $po_item = $event->po_item;
        DB::table('purchase_orders')
            ->where('id', $po_item->po_id)
            ->update(['delivery_completed' => 0]);
    }
}
