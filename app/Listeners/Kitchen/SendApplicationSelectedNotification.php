<?php

namespace App\Listeners\Kitchen;

use App\Events\Kitchen\ApplicationSelected;
use App\Notifications\Kitchen\ApplicationSelectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendApplicationSelectedNotification implements ShouldQueue {
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param ApplicationSelected $event
     * @return void
     */
    public function handle(ApplicationSelected $event) {
        $event->application->kitchen->user->notify(new ApplicationSelectedNotification($event->application));
    }
}
