<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MessageSentNotification extends Notification implements ShouldQueue{
	use Queueable;
	private $message;
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($message) {
		$this->message = $message;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		$channels = $this->message['channels'];
		return $channels;
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		$body = explode(PHP_EOL,$this->message['body']);
		$email = (new MailMessage)
			->subject($this->message['subject'])
			->greeting(__('notification.greeting', ['name' => $notifiable->name]));


		foreach ($body as $line) {
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

	public function toNexmo($notifiable){
		return (new NexmoMessage)
			->content($this->message['body']);
	}
}
