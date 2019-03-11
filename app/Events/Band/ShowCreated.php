<?php

namespace App\Events\Band;

use App\Models\BandSchedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShowCreated {
	use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var BandSchedule
	 */
	public $show;
	
	/**
	 * Create a new event instance.
	 *
	 * @param BandSchedule $schedule
	 */
	public function __construct(BandSchedule $show) {
		//
		$this->show = $show;
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
