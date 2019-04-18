<?php

namespace App\Notifications\Band;

use App\Models\BandSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShowUpdatedNotification extends Notification {
	use Queueable;
	/**
	 * @var BandSchedule
	 */
	private $newSchedule;
	/**
	 * @var BandSchedule
	 */
	private $oldSchedule;
	
	/**
	 * Create a new notification instance.
	 *
	 * @param BandSchedule $newSchedule
	 * @param BandSchedule $oldSchedule
	 */
	public function __construct(BandSchedule $newSchedule, BandSchedule $oldSchedule) {
		//
		$this->newSchedule = $newSchedule;
		$this->oldSchedule = $oldSchedule;
	}
	
	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}
	
	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		$message = collect();
		if ($this->newSchedule->stage_id != $this->oldSchedule->stage_id) {
			$message = $message->concat(explode(PHP_EOL, app('settings')->get("schedule_stage_changed_{$notifiable->language}")));
		}
		
		if ($this->newSchedule->payment != $this->oldSchedule->payment) {
			if (count($message) > 0) {
				$message->push('');
			}
			$message = $message->concat(explode(PHP_EOL, app('settings')->get("schedule_payment_changed_{$notifiable->language}")));
		}
		
		$email = (new MailMessage)
			->from(env('MAIL_BANDS_FROM_ADDRESS'))
			->subject(app('settings')->get("schedule_changed_subject{$notifiable->language}"))
			->greeting(__('notification.greeting', ['name' => $notifiable->name]));
		
		
		foreach ($message as $line) {
			$email->line($line);
		}
		
		return $email;
	}
	
	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable) {
		return [
			//
		];
	}
}
