<?php

namespace App\Listeners\BandMember;

use App\Events\BandMember\BandMemberProfileFilled;
use App\Notifications\BandMember\ProfileFilledNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBandMemberProfileFilledNotification {
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
	 * @param BandMemberProfileFilled $event
	 * @return void
	 */
	public function handle(BandMemberProfileFilled $event) {
		$event->bandMember->user->notify(new ProfileFilledNotification());
	}
}
