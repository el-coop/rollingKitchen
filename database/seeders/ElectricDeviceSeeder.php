<?php

namespace Database\Seeders;

use App\Models\ElectricDevice;
use Illuminate\Database\Seeder;

class ElectricDeviceSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        \App\Models\Application::all()->each(function ($application) {
            $products = rand(1, 4);
            for ($j = 0; $j < $products; $j++) {
                $application->electricDevices()->save(ElectricDevice::factory()->make());
            }
        });
    }
}
