<?php

use App\Models\Accountant;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountantSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(Accountant::class)->create()->each(function ($accountant){
			$user = factory(User::class)->make([
				'email' => app('settings')->get('accountant_email'),
				'password' => bcrypt(123456)
			]);
			$accountant->user()->save($user);
		});
	}
}
