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

class ShowUpdated {
	use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var BandSchedule
	 */
	public $show;
	/**
	 * @var BandSchedule
	 */
	public $oldShow;
	
	/**
	 * Create a new event instance.
	 *
	 * @param BandSchedule $show
	 * @param BandSchedule $oldShow
	 */
	public function __construct(BandSchedule $show, BandSchedule $oldShow) {
		//
		$this->show = $show;
		$this->oldShow = $oldShow;
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
