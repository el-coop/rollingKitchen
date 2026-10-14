<?php

namespace App\Notifications\Kitchen;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSelectedNotification extends Notification implements ShouldQueue {
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
        $this->application = $application;
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
        $message = explode(PHP_EOL, app('settings')->get("application_selected_email_{$notifiable->language}"));
        $email = (new MailMessage)
            ->subject(app('settings')->get("application_selected_subject_{$notifiable->language}"))
            ->greeting(__('notification.greeting', ['name' => $notifiable->name], $notifiable->language));

        foreach ($message as $line) {
            $email->line($line);
        }

        return $email->action(__('notification.fillExtraInfo', [], $notifiable->language), action('Kitchen\KitchenController@edit', $this->application->kitchen));
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
