<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Workplace;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */

    public function run() {
        Shift::factory(10)->make()->each(function ($shift) {
            $workplace = Workplace::inRandomOrder()->limit(1)->first();
            $workplace->shifts()->save($shift);
        });
    }
}


