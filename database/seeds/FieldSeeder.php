<?php

use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
		factory(\App\Models\Field::class,5)->create();
		factory(\App\Models\Field::class,5)->create([
			'form' => \App\Models\Application::class,
		]);
    }
}