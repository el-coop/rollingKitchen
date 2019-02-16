<?php

namespace App\Events\Worker;

use App\Models\Worker;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaxReviewUploaded {
	use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var Worker
	 */
	public $worker;
	
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Worker $worker) {
		//
		$this->worker = $worker;
	}
	
	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn() {
		return new PrivateChannel('channel-name');
	}
}
