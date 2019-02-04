<?php

use App\Models\Shift;
use App\Models\Workplace;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run() {
		factory(Shift::class, 10)->make()->each(function ($shift) {
			$workplace = Workplace::inRandomOrder()->limit(1)->first();
			$workplace->shifts()->save($shift);
		});
	}
}


