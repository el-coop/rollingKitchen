<?php

namespace Database\Seeders;

use App\Models\Worker;
use App\Models\WorkerApplication;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddWorkerAppToSubmittedWorkers extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $year = Carbon::now()->year;
        $unSubmittedWorkers = Worker::where('last_submitted', '!=', $year)->get();
        foreach ($unSubmittedWorkers as $worker) {
            $worker->applications()->delete();
        }

    }
}
