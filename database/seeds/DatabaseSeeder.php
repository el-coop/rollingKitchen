<?php


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
		$this->call(AdminSeeder::class);
		$this->call(DeveloperSeeder::class);
		$this->call(FieldSeeder::class);
		$this->call(ServiceSeeder::class);
		$this->call(SettingsSeeder::class);
		
		$this->call(KitchenSeeder::class);
		$this->call(PhotosSeeder::class);
		$this->call(ApplicationSeeder::class);
		$this->call(ProductSeeder::class);
		$this->call(ElectricDeviceSeeder::class);
		$this->call(InvoiceSeeder::class);
		$this->call(WorkplaceSeeder::class);
		$this->call(WorkerSeeder::class);
		$this->call(ShiftSeeder::class);
		$this->call(SupervisorSeeder::class);
		$this->call(BandSeeder::class);
		$this->call(StageSeeder::class);
//		$this->call(AccountantSeeder::class);
	
	
	}
}
