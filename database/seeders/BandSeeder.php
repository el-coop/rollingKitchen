<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\User;
use Illuminate\Database\Seeder;

class BandSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Band::factory()->create()->each(function ($band) {
            $user = User::factory()->make([
                'email' => 'band@kitchen.test',
                'password' => bcrypt(123456)
            ]);
            $band->user()->save($user);
        });
        Band::factory(9)->create()->each(function ($band) {
            $band->user()->save(User::factory()->make());
        });
    }
}
