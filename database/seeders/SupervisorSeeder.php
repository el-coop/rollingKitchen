<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Seeder;
use App\Models\Worker;

class SupervisorSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        $supervisor = Worker::factory()->create(['supervisor' => true]);
        $user = User::factory()->make(['email' => 'supervisor@elcoop.io', 'password' => bcrypt(123456)]);
        $supervisor->user()->save($user);
        $workplaces = Workplace::inRandomOrder()->limit(2)->get();
        $supervisor->workplaces()->attach($workplaces);
        Worker::factory(5)->create()->each(function ($worker) use ($workplaces) {
            $user = User::factory()->make();
            $worker->user()->save($user);
            $worker->workplaces()->attach($workplaces);
        });

    }
}
