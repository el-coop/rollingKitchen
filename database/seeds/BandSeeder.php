<?php

use App\Models\Band;
use App\Models\User;
use Illuminate\Database\Seeder;

class BandSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(Band::class)->create()->each(function ($band){
			$user = factory(User::class)->make([
				'email' => 'band@kitchen.test',
				'password' => bcrypt(123456)
			]);
			$band->user()->save($user);
		});
		factory(Band::class, 9)->create()->each(function ($band) {
			$band->user()->save(factory(\App\Models\User::class)->make());
		});
	}
}
