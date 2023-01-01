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
		return $this->from(env('BACKUP_MAIL_FROM'))->subject(env('APP_NAME') . ' backup for ' . Carbon::now()->toDateString())->view('mail.raw')->with('body', 'Backup for ' . Carbon::now()->toDateString() . ' is attached')
			->attach(storage_path('app/backups/backup.sql'));
	}

}
