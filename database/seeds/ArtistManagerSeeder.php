<?php

use App\Models\ArtistManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArtistManagerSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(ArtistManager::class)->create()->each(function ($band) {
			$user = factory(User::class)->make([
				'email' => 'am@kitchen.test',
				'password' => bcrypt(123456)
			]);
			$band->user()->save($user);
		});
		factory(ArtistManager::class, 9)->create()->each(function ($band) {
			$band->user()->save(factory(User::class)->make());
		});
	}
}
