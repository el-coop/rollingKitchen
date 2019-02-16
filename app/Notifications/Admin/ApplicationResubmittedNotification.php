<?php

namespace App\Notifications\Admin;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationResubmittedNotification extends Notification implements ShouldQueue {
	use Queueable;
	/**
	 * @var Application
	 */
	private $application;
	
	/**
	 * Create a new notification instance.
	 *
	 * @param Application $application
	 */
	public function __construct(Application $application) {
		//
		$this->application = $application;
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
		return (new MailMessage)
			->line(__('notification.resubmitted', ['name' => $this->application->kitchen->user->name]))
			->action(__('notification.clickToView'), action('Admin\ApplicationController@show', $this->application));
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
