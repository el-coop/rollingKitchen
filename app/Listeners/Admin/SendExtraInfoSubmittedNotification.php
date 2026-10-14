<?php

namespace App\Listeners\Admin;

use App\Events\Kitchen\ExtraInfoSubmitted;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\Admin\ExtraInfoSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExtraInfoSubmittedNotification implements ShouldQueue {
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param ExtraInfoSubmitted $event
     * @return void
     */
    public function handle(ExtraInfoSubmitted $event) {
        $admin = User::where('user_type', Admin::class)->first();
        $admin->notify(new ExtraInfoSubmittedNotification($event->application));
    }
}
