<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BackupEmail extends Mailable {
	use Queueable, SerializesModels;
	
	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->usingBackupSmtp()->subject('Backup for ' . Carbon::now()->toDateString())->view('mail.raw')->with('body', 'Backup for ' . Carbon::now()->toDateString() . ' is attached')
			->attach(storage_path('app/backups/backup.sql'));
	}
	
	private function usingBackupSmtp() {
		$mailTransport = app()->make('mailer')
			->getSwiftMailer()
			->getTransport();
		
		if ($mailTransport instanceof \Swift_SmtpTransport) {
			/** @var \Swift_SmtpTransport $mailTransport */
			$mailTransport->setHost(env('BACKUP_MAIL_HOST'));
			$mailTransport->setPort(env('BACKUP_MAIL_PORT'));
			$mailTransport->setEncryption(env('BACKUP_MAIL_ENCRYPTION'));
			$mailTransport->setUsername(env('BACKUP_MAIL_USERNAME'));
			$mailTransport->setPassword(env('BACKUP_MAIL_PASSWORD'));
		}
		
		return $this;
	}
}
