<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceSent extends Notification {
	use Queueable;
	/**
	 * @var string
	 */
	private $subject;
	/**
	 * @var string
	 */
	private $body;
	/**
	 * @var array
	 */
	private $files;
	/**
	 * @var array
	 */
	private $bcc;
	/**
	 * @var string
	 */
	private $language;
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * Create a new notification instance.
	 *
	 * @param string $subject
	 * @param string $name
	 * @param string $body
	 * @param string $language
	 * @param array $files
	 * @param array $bcc
	 */
	public function __construct(string $subject, string $name, string $body, string $language, array $files, array $bcc) {
		//
		$this->subject = $subject;
		$this->body = $body;
		$this->files = $files;
		$this->bcc = $bcc;
		$this->language = $language;
		$this->name = $name;
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
		
		$email = (new MailMessage)
			->subject($this->subject)
			->greeting(__('notification.greeting', ['name' => $this->name], $this->language))
			->line($this->body)
			->bcc($this->bcc);
		
		foreach ($this->files as $file) {
			$email->attach($file['file'], [
				'mime' => 'application/pdf',
				'as' => $file['name']
			]);
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
