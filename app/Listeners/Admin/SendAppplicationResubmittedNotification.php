<?php

namespace App\Listeners\Admin;

use App\Events\Kitchen\ApplicationResubmitted;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\Admin\ApplicationResubmittedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppplicationResubmittedNotification implements ShouldQueue {
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
	 * @param  ApplicationResubmitted $event
	 * @return void
	 */
	public function handle(ApplicationResubmitted $event) {
		$admins = User::where('user_type', Admin::class)->get();
		$admins->each->notify(new ApplicationResubmittedNotification($event->application));
	}
}
