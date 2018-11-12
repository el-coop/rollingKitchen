<?php

use Illuminate\Database\Seeder;

class KitchenSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(\App\Models\Kitchen::class, 100)->create()->each(function ($kitchen) {
			$user = factory(\App\Models\User::class)->make();
			$kitchen->user()->save($user);
			$imagesNumber = rand(0, 4);
			for ($i = 0; $i < $imagesNumber; $i++) {
				$kitchen->photos()->save(factory(\App\Models\Photo::class)->make());
			}
		});
	}
}
