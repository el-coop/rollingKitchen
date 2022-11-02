<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Admin::factory()->create()->each(function ($admin) {
            $user = User::factory()->make([
                'email' => 'admin@kitchen.com',
                'password' => bcrypt(123456)
            ]);
            $admin->user()->save($user);
        });
    }
}
