<?php

namespace App\Listeners\Admin;

use App\Events\Kitchen\ApplicationSubmitted;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\Admin\ApplicationResubmittedNotification;
use App\Notifications\Admin\ApplicationSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * @param ApplicationSubmitted $event
     * @return void
     */
    public function handle(ApplicationSubmitted $event) {
        $admins = User::where('user_type', Admin::class)->get();
        $admins->each->notify(new ApplicationSubmittedNotification($event->application));
        
    }
}
