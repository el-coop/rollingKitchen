<?php

namespace App\Listeners\Band;

use App\Notifications\Band\ShowCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShowCreatedNotification {
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
	 * @param  object $event
	 * @return void
	 */
	public function handle($event) {
		$event->show->band->user->notify(new ShowCreatedNotification);
		
	}
}
