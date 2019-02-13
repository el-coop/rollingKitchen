<?php

use App\Models\Developer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
		factory(Developer::class)->create()->each(function ($developer){
			$user = factory(User::class)->make([
				'email' => 'developer@elcoop.io',
				'password' => bcrypt(123456)
			]);
			$developer->user()->save($user);
		});
    }
}
