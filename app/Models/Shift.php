<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model {
	protected $casts = [
		'closed' => 'boolean',
	];
	
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
}
