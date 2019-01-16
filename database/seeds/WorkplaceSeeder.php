<?php

use App\Models\WorkFunction;
use App\Models\Workplace;
use Illuminate\Database\Seeder;

class WorkplaceSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(Workplace::class,10)->create()->each(function ($workplace){
			factory(WorkFunction::class, 5)->make()->each(function ($workFunction) use ($workplace) {
				$workplace->workFunctions()->save($workFunction);
			});
		});
	}
}
