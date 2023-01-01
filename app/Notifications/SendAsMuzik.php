<?php


namespace App\Notifications;


use Illuminate\Notifications\Messages\MailMessage;

trait SendAsMuzik {
    protected function usingMusicSmtp() {
        $from = env('MAIL_BANDS_USERNAME');

        return (new MailMessage)->mailer('muzik_smtp')->from($from);
    }
}
