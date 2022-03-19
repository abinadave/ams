<?php

namespace App\Listeners;

use App\Events\RisItemUpdatedorDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class updateRisItemList
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
     * @param  RisItemUpdatedorDeleted  $event
     * @return void
     */
    public function handle(RisItemUpdatedorDeleted $event)
    {
        Log::info('request_form_id', array('ris_Form_id', $event->request_form_id));
    }
}
