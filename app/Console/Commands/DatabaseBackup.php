<?php

namespace App\Console\Commands;

use App\Mail\BackupEmail;
use App\Models\Developer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DatabaseBackup extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'db:backup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Backs up the database and mails it to Developers';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Spatie\DbDumper\Exceptions\CannotStartDump
	 * @throws \Spatie\DbDumper\Exceptions\DumpFailed
	 */
	public function handle() {
		\Spatie\DbDumper\Databases\MySql::create()
			->setDbName(env('DB_DATABASE'))
			->setUserName(env('DB_USERNAME'))
			->setPassword(env('DB_PASSWORD'))
            ->doNotCreateTables()
            ->excludeTables('migrations')
            ->addExtraOption('--complete-insert')
			->dumpToFile(storage_path('app/backups/backup.sql'));

		$this->sendBackup();
	}

	private function sendBackup() {
		Mail::mailer('backup_smtp')->to(Developer::first()->user)->cc(env('BACKUP_MAIL_DESTINATION'))->send(new BackupEmail);
	}

}
