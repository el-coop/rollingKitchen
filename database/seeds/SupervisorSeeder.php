<?php

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Seeder;
use App\Models\Worker;

class SupervisorSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$supervisor = factory(Worker::class)->create(['supervisor' => true]);
		$user = factory(User::class)->make(['email' => 'supervisor@elcoop.io', 'password' => bcrypt(123456)]);
		$supervisor->user()->save($user);
		$workplaces = Workplace::inRandomOrder()->limit(2)->get();
		$supervisor->workplaces()->attach($workplaces);
		factory(Worker::class, 5)->create()->each(function ($worker) use ($workplaces){
			$user = factory(User::class)->make();
			$worker->user()->save($user);
			$worker->workplaces()->attach($workplaces);
		});

	}
}
