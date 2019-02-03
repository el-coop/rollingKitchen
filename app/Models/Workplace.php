<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workplace extends Model {

	public function workFunctions() {
		return $this->hasMany(WorkFunction::class);
	}

	public function shifts() {
		return $this->hasMany(Shift::class);
	}

	public function getFullDataAttribute() {
		return collect([[
			'name' => 'name',
			'label' => __('global.name'),
			'type' => 'text',
			'value' => $this->name
		], [
			'name' => 'workFunctions',
			'value' => $this->workFunctions
		]]);
	}

	public function workers() {
		return $this->belongsToMany(Worker::class)->withTimestamps();
	}

	public function getWorkersForSupervisorAttribute() {
		return [
			'model' => Worker::class,
			'where' => [['user_type', Worker::class], ['workplace_id', $this->id], ['supervisor', false]],
			'joins' => [
				['users', 'users.user_id', 'workers.id'],
				['worker_workplace', 'worker_workplace.worker_id', 'workers.id']
			],
			'fields' => [[
				'name' => 'workers.id',
				'title' => 'id',
				'visible' => false,
			], [
				'name' => 'name',
				'table' => 'users',
				'title' => __('global.name'),
				'sortField' => 'name',
			], [
				'name' => 'workplacesList',
				'noTable' => true,
				'title' => __('admin/workers.workplaces'),
			], [
				'name' => 'completed',
				'noTable' => true,
				'title' => __('admin/workers.completed'),
			]],

		];

	}

	public function hasWorker(Worker $worker) {
		return $this->workers->contains($worker);
	}

	public function getShiftsForSupervisorAttribute() {
		return [
			'model' => Shift::class,
			'where' => [['workplace_id', $this->id]],
			'fields' => [[
				'name' => 'id',
				'title' => 'id',
				'visible' => false,
			],[
				'name' => 'date',
				'title' => __('admin/shifts.date'),
				'sortField' => 'date',
				'callback' => 'date'
			], [
				'name' => 'hours',
				'title' => __('admin/shifts.hours'),
				'sortField' => 'hours',
				'callback' => 'localNumber',
				'filter' => false,]],

		];
	}
}
