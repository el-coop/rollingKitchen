<?php

namespace App\Events\Band;

use App\Models\BandAdmin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BandAdminProfileFilled {
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $bandAdmin;

	/**
	 * Create a new event instance.
	 *
	 * @param BandAdmin $bandAdmin
	 *
	 */
	public function __construct(BandAdmin $bandAdmin) {
		//
		$this->bandAdmin = $bandAdmin;
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
