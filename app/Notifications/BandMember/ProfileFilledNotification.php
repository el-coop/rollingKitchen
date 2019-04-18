<?php

namespace App\Notifications\BandMember;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileFilledNotification extends Notification {
	use Queueable;
	
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}
	
	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}
	
	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		$message = explode(PHP_EOL, app('settings')->get("band_members_profile_filled_{$notifiable->language}"));
		
		$email = (new MailMessage)
			->from(env('MAIL_BANDS_FROM_ADDRESS'))
			->subject(app('settings')->get("band_members_profile_filled_subject_{$notifiable->language}"))
			->greeting(__('notification.greeting', ['name' => $notifiable->name]));
		
		
		foreach ($message as $line) {
			$email->line($line);
		}
		
		return $email;
	}
	
	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable) {
		return [
			//
		];
	}
}
