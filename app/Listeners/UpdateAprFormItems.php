<?php

namespace App\Listeners;

use App\Events\AprItemCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\AprForm;
class UpdateAprFormItems
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
     * @param  AprItemCreated  $event
     * @return void
     */
    public function handle(AprItemCreated $event)
    {
        
        $collection = collect($event->items);
        $plucked = $collection->pluck('name');
        $apr_id = $event->apr_id;
        $item_names = $plucked->all();
        AprForm::where('id', $apr_id)->update(['items' => join(", ", $item_names)]);
        // Log::info('names', array('items'=> $plucked->all()));
    }
}
