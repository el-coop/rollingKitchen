<?php

use App\Models\Photo;
use Illuminate\Database\Seeder;

class PhotosSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		\App\Models\Kitchen::all()->each(function ($kitchen) {
			$imagesNumber = rand(0, 4);
			
			for ($i = 0; $i < $imagesNumber; $i++) {
				$kitchen->photos()->save(factory(Photo::class)->make());
			}
			
		});
	}
}
