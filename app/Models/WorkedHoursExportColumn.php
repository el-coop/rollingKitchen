<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class WorkedHoursExportColumn extends Model {
    use HasFactory;

    static public function options() {
		$options = collect([
			'user.name' => __('global.name'),
			'shift.date' => __('admin/shifts.date'),
			'user.email' => __('global.email'),
			'shift_worker.start_time' => __('admin/shifts.startTime'),
			'shift_worker.end_time' => __('admin/shifts.endTime'),
			'shift_worker.workFunction' => __('worker/supervisor.workFunction'),
			'shift_worker.payment' => __('admin/workers.shiftPayment'),
			'worker.totalPayment' => __('admin/workers.totalPayment'),
			'worker.type' => __('admin/workers.type'),
			'shift.workplace_id' => __('admin/workers.workplace'),
			'worker.workedHours' => __('worker/worker.workedHours'),
			'worker.pdf' => __('admin/workers.pdf')
		]);
		Field::where('form', Worker::class)->get()->each(function ($workerColumn) use ($options) {
			$options->put(
				'worker.' . $workerColumn->id, $workerColumn->name_nl);
		});
		return $options;
	}
}
