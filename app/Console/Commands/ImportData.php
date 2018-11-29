<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Console\Command;

class ImportData extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:data';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports all prepared data from the past';
	
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
	 */
	public function handle() {
		$this->call('migrate:fresh');
		factory(Admin::class)->create()->user()->save(factory(User::class)->make([
			'name' => 'admin',
			'email' => 'admin@kitchen.com',
			'password' => bcrypt(123456)
		]));
		$this->call('import:services');
		$this->call('import:fields');
		$this->call('import:kitchens');
	}
}
