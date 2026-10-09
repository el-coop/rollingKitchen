<?php

namespace App\Events\Kitchen;

use App\Models\Application;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationSelected {
    use Dispatchable, SerializesModels;

    /**
     * @var Application
     */
    public $application;

    /**
     * Create a new event instance.
     *
     * @param Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
}
