<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Seeder;
use App\Models\Worker;

class WorkerSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Worker::factory(25)->create()->each(function ($worker) {
            $user = User::factory()->make();
            $worker->user()->save($user);
            $workplaces = Workplace::inRandomOrder()->limit(2)->get();
            $worker->workplaces()->attach($workplaces);
        });
    }
}
