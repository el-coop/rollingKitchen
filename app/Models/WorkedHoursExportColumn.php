<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class WorkedHoursExportColumn extends Model {

	static public function getOptionsAttribute(){
		$options = collect([
			'user.name' => __('global.name'),
			'user.email' => __('global.email'),
			'shift_worker.start_time' => __('admin/shifts.startTime'),
			'shift_worker.end_time' => __('admin/shifts.endTime'),
			'shift_worker.work_function_id' => __('worker/supervisor.workFunctions'),
			'worker.type' => __('admin/workers.type'),
			'shift.workplace_id' => __('admin/workers.workplace'),
			]);
		$workerColumns = Field::where('form', Worker::class)->get()->pluck('name_nl');
		foreach ($workerColumns as $workerColumn){
			$options->put(
				'worker.' . $workerColumn, $workerColumn
			);
		}
		return $options->toArray();
	}
}
