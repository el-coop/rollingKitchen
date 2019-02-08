<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class WorkedHoursExportColumn extends Model {

	static public function getOptionsAttribute() {
		$options = collect([
			'user.name' => __('global.name'),
			'shift.date' => __('admin/shifts.date'),
			'user.email' => __('global.email'),
			'shift_worker.start_time' => __('admin/shifts.startTime'),
			'shift_worker.end_time' => __('admin/shifts.endTime'),
			'shift_worker.work_function_id' => __('worker/supervisor.workFunctions'),
			'worker.type' => __('admin/workers.type'),
			'shift.workplace_id' => __('admin/workers.workplace'),
		]);
		Field::where('form', Worker::class)->get()->each(function ($workerColumn) use ($options) {
			$options->put(
				'worker.' . $workerColumn->id, $workerColumn->name_nl);
		});
		return $options;
	}
}
