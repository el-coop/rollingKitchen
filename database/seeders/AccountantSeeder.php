<?php

namespace Database\Seeders;

use App\Models\Accountant;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountantSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Accountant::factory()->create()->each(function ($accountant) {
            $user = User::factory()->make([
                'email' => app('settings')->get('accountant_email'),
                'password' => bcrypt(123456)
            ]);
            $accountant->user()->save($user);
        });
    }
}
