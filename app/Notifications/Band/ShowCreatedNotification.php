<?php

namespace App\Notifications\Band;

use App\Notifications\SendAsMuzik;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShowCreatedNotification extends Notification {
    use Queueable;
    use SendAsMuzik;
    
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
        $message = explode(PHP_EOL, app('settings')->get("schedule_created_{$notifiable->language}"));
        
        $email = $this->usingMusicSmtp()
            ->subject(app('settings')->get("schedule_created_subject_{$notifiable->language}"))
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
