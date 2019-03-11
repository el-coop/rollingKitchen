<?php

namespace App\Listeners\Worker;

use App\Events\Worker\WorkerProfileFilled;
use App\Notifications\Worker\ProfileFilledNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProfileFilledNotification {
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
	public function handle(WorkerProfileFilled $event) {
		$event->worker->user->notify(new ProfileFilledNotification());
	}
}
