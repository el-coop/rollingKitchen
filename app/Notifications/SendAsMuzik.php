<?php


namespace App\Notifications;


use Illuminate\Notifications\Messages\MailMessage;

trait SendAsMuzik {
    protected function usingMusicSmtp() {
        $mailTransport = app()->make('mailer')
            ->getSwiftMailer()
            ->getTransport();
        
        $from = env('MAIL_BANDS_USERNAME');
        
        if ($mailTransport instanceof \Swift_SmtpTransport) {
            /** @var \Swift_SmtpTransport $mailTransport */
            $mailTransport->setHost(env('MAIL_HOST'));
            $mailTransport->setPort(env('MAIL_PORT'));
            $mailTransport->setEncryption(env('MAIL_ENCRYPTION'));
            $mailTransport->setUsername($from);
            $mailTransport->setPassword(env('MAIL_BANDS_PASSWORD'));
        }
        
        return (new MailMessage)->from($from);
    }
}
