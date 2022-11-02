<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        Field::factory(7)->create();
        Field::factory(3)->create([
            'form' => \App\Models\Application::class,
        ]);
    }
}
