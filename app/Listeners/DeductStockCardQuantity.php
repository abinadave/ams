<?php

namespace App\Listeners;

use App\Events\EventRequestItemCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use DB;
use Auth;   
use App\Activity;
use App\Item;
class DeductStockCardQuantity
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
     * @param  EventRequestItemCreated  $event
     * @return void
     */
    public function handle(EventRequestItemCreated $event)
    {
        $r_item = $event->requestItem;
        $ldate = date('m-d-Y H:i:s');
        # $r_item->item_id
        DB::table('items')->where('id', $r_item->item_id)->decrement('running_balance', $r_item->requested_qty);
        Item::where('id', $r_item->item_id)->update(['running_bal_remarks' => 'As of ' . $ldate]);
        $act = new Activity;
        $act->activity = 'Item unique id: ' . $r_item->item_id . ', qty withdraw: ' . $r_item->requested_qty . ', previous balance: ' . $r_item->qty_before_withdraw;
        $act->user_id = Auth::user()->id;
        $act->category = 'Issuances / Requisition';
        $act->save();
    }
}
