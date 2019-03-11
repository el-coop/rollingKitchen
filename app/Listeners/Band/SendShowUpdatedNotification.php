<?php

namespace App\Listeners\Band;

use App\Events\Band\ShowUpdated;
use App\Notifications\Band\ShowUpdatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShowUpdatedNotification {
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}
	
	/**
	 * Handle the event.
	 *
	 * @param  ShowUpdated $event
	 * @return void
	 */
	public function handle(ShowUpdated $event) {
		$event->show->band->user->notify(new ShowUpdatedNotification($event->show, $event->oldShow));
	}
}
