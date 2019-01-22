<?php

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Seeder;
use App\Models\Worker;

class WorkerSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory(Worker::class, 10)->create()->each(function ($worker) {
			$user = factory(User::class)->make();
			$worker->user()->save($user);
			$workplaces = Workplace::inRandomOrder()->limit(2)->get();
			$worker->workplaces()->attach($workplaces);
		});
	}
}
