<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class ImportServices extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:services';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports services from services.json file in storage';
	
	
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
		$this->info('Reading File...');
		$servicesJson = json_decode(\Storage::disk('local')->get('services.json'), true);
		$this->info("Importing services");
		
		foreach ($servicesJson as $service) {
			Service::forceCreate($service);
		}
		
		$this->alert('Services import Complete');
		
	}
}
