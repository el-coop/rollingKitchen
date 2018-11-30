<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\ElectricDevice;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Console\Command;

class ImportKitchens extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:kitchens';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports kitchens from kitchens.json file in storage';
	
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
		$kitchens = json_decode(\Storage::disk('local')->get('kitchens.json'), true);
		$this->info('Importing ' . count($kitchens) . ' kitchens');
		$i = 0;
		foreach ($kitchens as $kitchen) {
			$i++;
			$kitchenModel = Kitchen::forceCreate([
				'status' => $kitchen['status'],
				'data' => $kitchen['data']
			]);
			
			
			$kitchenModel->user()->save((new User())->forceFill($kitchen['user']));
			
			foreach ($kitchen['applications'] as $application) {
				$applicationModel = new Application;
				$applicationModel->forceFill(collect($application)->except([
					'products',
					'devices',
					'services'
				])->toArray());
				$kitchenModel->applications()->save($applicationModel);
				foreach ($application['products'] as $product) {
					$productModel = new Product;
					$productModel->forceFill($product);
					$applicationModel->products()->save($productModel);
				}
				foreach ($application['devices'] as $device) {
					$deviceModel = new ElectricDevice;
					$deviceModel->forceFill($device);
					$applicationModel->electricDevices()->save($deviceModel);
					
				}
				
				foreach ($application['services'] as $service => $quantity) {
					if($quantity){
						$applicationModel->services()->attach(($service+1),['quantity' => $quantity]);
					}
				}
			}
			if ($i % 10 === 0) {
				$this->info("Imported {$i} kitchens");
			}
		}
		$this->alert('Kitchen import Complete');
		
	}
}
