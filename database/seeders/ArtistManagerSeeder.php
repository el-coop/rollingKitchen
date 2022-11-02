<?php

namespace Database\Seeders;

use App\Models\ArtistManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArtistManagerSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        ArtistManager::factory()->create()->each(function ($band) {
            $user =  User::factory()->make([
                'email' => 'am@kitchen.test',
                'password' => bcrypt(123456)
            ]);
            $band->user()->save($user);
        });
        ArtistManager::factory(9)->create()->each(function ($band) {
            $band->user()->save( User::factory()->make());
        });
    }
}
