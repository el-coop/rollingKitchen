<?php

namespace App\Listeners\Kitchen;

use App\Events\Kitchen\ApplicationSubmitted;
use App\Notifications\Kitchen\ApplicationSubmittedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendApplicationSubmittedNotification implements ShouldQueue {
	use InteractsWithQueue;
	
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
	 * @param  ApplicationSubmitted $event
	 * @return void
	 */
	public function handle(ApplicationSubmitted $event) {
		$event->application->kitchen->user->notify(new ApplicationSubmittedNotification);
	}
}
