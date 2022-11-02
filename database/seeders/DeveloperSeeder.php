<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Developer::factory()->create()->each(function ($developer) {
            $user = User::factory()->make([
                'email' => 'developer@elcoop.io',
                'password' => bcrypt(123456)
            ]);
            $developer->user()->save($user);
        });
    }
}
