<?php

namespace App\Events\BandMember;

use App\Models\BandMember;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BandMemberProfileFilled {
	use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var BandMember
	 */
	public $bandMember;
	
	/**
	 * Create a new event instance.
	 *
	 * @param BandMember $bandMember
	 */
	public function __construct(BandMember $bandMember) {
		//
		$this->bandMember = $bandMember;
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
