<?php

use Illuminate\Database\Seeder;

class StageSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(\App\Models\Stage::class, 4)->create();
	}
}
