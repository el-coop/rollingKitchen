<?php

namespace App\Notifications\Band;

use App\Notifications\SendAsMuzik;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification {
	use Queueable;
	use SendAsMuzik;
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
		$message = explode(PHP_EOL, app('settings')->get("bands_user_created_en"));
		$email = $this->usingMusicSmtp()
			->subject(app('settings')->get("bands_user_created_subject_en"))
			->greeting(__('notification.greeting', ['name' => 'nur']));


		foreach ($message as $line) {
			$email->line($line);
		}

		$email->action(__('admin/workers.fillProfile', [], 'en'), action('Band\BandController@showResetForm', $this->token, true));

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
