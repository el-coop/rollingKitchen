<?php

namespace App\Providers;

use App\Events\Band\ShowCreated;
use App\Events\Band\ShowDeleted;
use App\Events\Band\ShowUpdated;
use App\Events\BandMember\BandMemberProfileFilled;
use App\Events\Kitchen\ApplicationResubmitted;
use App\Events\Kitchen\ApplicationSubmitted;
use App\Events\Worker\WorkerProfileFilled;
use App\Events\Worker\TaxReviewUploaded;
use App\Listeners\Admin\SendApplicationSubmittedNotification;
use App\Listeners\Admin\SendAppplicationResubmittedNotification;
use App\Listeners\Band\SendShowCreatedNotification;
use App\Listeners\Band\SendShowDeletedNotification;
use App\Listeners\Band\SendShowUpdatedNotification;
use App\Listeners\BandMember\SendBandMemberProfileFilledNotification;
use App\Listeners\Kitchen\SendApplicationSubmittedNotification as KitchenApplicationSubmittedNotification;
use App\Listeners\Worker\SendProfileFilledNotification;
use App\Listeners\Worker\SendTaxReviewNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ApplicationSubmitted::class => [
            KitchenApplicationSubmittedNotification::class,
            SendApplicationSubmittedNotification::class
        ],
        ApplicationResubmitted::class => [
            SendAppplicationResubmittedNotification::class
        ],
        TaxReviewUploaded::class => [
            SendTaxReviewNotification::class
        ],
        WorkerProfileFilled::class => [
            SendProfileFilledNotification::class
        ],
        ShowDeleted::class => [
            SendShowDeletedNotification::class
        ],
        ShowUpdated::class => [
            SendShowUpdatedNotification::class
        ],
        ShowCreated::class => [
            SendShowCreatedNotification::class
        ],
        BandMemberProfileFilled::class => [
            SendBandMemberProfileFilledNotification::class
        ]
    ];
    
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        parent::boot();
        
        //
    }
}
