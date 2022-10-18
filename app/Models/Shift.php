<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model {
    use HasFactory;

    protected $casts = [
		'closed' => 'boolean',
	];

	protected static function boot() {
		parent::boot();
		static::deleted(function ($shift) {
			$shift->workers()->detach();
		});
	}

	public function workplace() {
		return $this->belongsTo(Workplace::class);
	}

	public function getFullDataAttribute() {
		return collect([[
			'name' => 'date',
			'label' => __('admin/shifts.date'),
			'type' => 'text',
			'subType' => 'date',
			'value' => $this->date
		], [
			'name' => 'workplace',
			'label' => __('worker/worker.workplace'),
			'type' => 'select',
			'options' => Workplace::select('id', 'name')->get()->pluck('name', 'id'),
			'value' => $this->workplace_id
		], [
			'name' => 'hours',
			'label' => __('admin/shifts.hours'),
			'type' => 'text',
			'subType' => 'number',
			'value' => $this->hours
		], [
			'name' => 'closed',
			'type' => 'checkbox',
			'value' => $this->closed,
			'options' => [[
				'name' => __('admin/settings.closed')
			]]
		]]);
	}

	public function workers() {
		return $this->belongsToMany(Worker::class)->using(ShiftWorker::class)->withPivot('start_time', 'end_time', 'work_function_id');
	}

	public function shiftWorkers() {
		return $this->hasMany(ShiftWorker::class);
	}

	public function getTotalHoursAttribute() {
		$totalHours = new Carbon('today');
		$startOfDay = $totalHours->clone();
		$this->shiftWorkers->each(function ($worker) use ($totalHours) {
			$totalHours->add($worker->workedHours);
		});
		return $startOfDay->diffAsCarbonInterval($totalHours);
	}
}
