<?php

namespace App\Listeners\Worker;

use App\Events\Worker\TaxReviewUploaded;
use App\Notifications\Worker\TaxReviewNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaxReviewNotification implements ShouldQueue {
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
	 * @param  TaxReviewUploaded $event
	 * @return void
	 */
	public function handle(TaxReviewUploaded $event) {
		$event->worker->user->notify(new TaxReviewNotification());
	}
}
