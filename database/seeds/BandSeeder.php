<?php

use Illuminate\Database\Seeder;

class BandSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(\App\Models\Band::class, 10)->create()->each(function ($band) {
			$band->user()->save(factory(\App\Models\User::class)->make());
		});
	}
}
