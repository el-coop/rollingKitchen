<?php

namespace App\Listeners\Band;

use App\Events\Band\ShowDeleted;
use App\Notifications\Band\ShowDeletedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShowDeletedNotification
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
     * @param  ShowDeleted  $event
     * @return void
     */
    public function handle(ShowDeleted $event)
    {
        $event->show->band->user->notify(new ShowDeletedNotification);
    }
}
