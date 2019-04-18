<?php

namespace App\Notifications\Band;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification {
	use Queueable;
	public $token;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($token) {
		//
		$this->token = $token;
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
		$message = explode(PHP_EOL, app('settings')->get("bands_user_created_{$notifiable->language}"));
		$email = (new MailMessage)
			->from(env('MAIL_BANDS_FROM_ADDRESS'))
			->subject(app('settings')->get("bands_user_created_subject_{$notifiable->language}"))
			->greeting(__('notification.greeting', ['name' => $notifiable->name]));


		foreach ($message as $line) {
			$email->line($line);
		}

		$email->action(__('admin/workers.fillProfile', [], $notifiable->language), action('Band\BandController@showResetForm', $this->token, true));

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
