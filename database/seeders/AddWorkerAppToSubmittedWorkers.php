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
        $submittedWorkers = Worker::whereNotNull('last_submitted')->get();
        $year = Carbon::now()->year;
        foreach ($submittedWorkers as $worker){
            if (!$worker->applications()->where('year', $year)->exists()){
                $workerApp = new WorkerApplication();
                $workerApp->year = $year;
                $worker->applications()->save($workerApp);
            }
        }
    }
}
