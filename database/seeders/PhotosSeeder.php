<?php

namespace Database\Seeders;

use App\Models\Photo;
use Illuminate\Database\Seeder;

class PhotosSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        \App\Models\Kitchen::all()->each(function ($kitchen) {
            $imagesNumber = rand(0, 4);

            for ($i = 0; $i < $imagesNumber; $i++) {
                $kitchen->photos()->save(Photo::factory()->make());
            }

        });
    }
}
