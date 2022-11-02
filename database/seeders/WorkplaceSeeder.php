<?php

namespace Database\Seeders;

use App\Models\WorkFunction;
use App\Models\Workplace;
use Illuminate\Database\Seeder;

class WorkplaceSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Workplace::factory(10)->create()->each(function ($workplace) {
            WorkFunction::factory(5)->make()->each(function ($workFunction) use ($workplace) {
                $workplace->workFunctions()->save($workFunction);
            });
        });
    }
}
