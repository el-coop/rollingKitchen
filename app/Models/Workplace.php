<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Auth;
use JustBetter\PaginationWithHavings\PaginationWithHavings;

class Workplace extends Model {
	use PaginationWithHavings;
	
	protected static function boot() {
		parent::boot();
		static::deleted(function ($workplace) {
			$workplace->workers()->detach();
			$workplace->workFunctions()->delete();
			$workplace->shifts->each->delete();
		});
	}
	
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
			'where' => [['user_type', Worker::class], ['workplace_id', $this->id], ['user_id', '!=', Auth::user()->user_id]],
			'joins' => [
				['users', 'users.user_id', 'workers.id'],
				['worker_workplace', 'worker_workplace.worker_id', 'workers.id'],
				['worker_photos', 'worker_photos.worker_id', 'workers.id']
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
				'filter' => false
			], [
				'name' => 'completed',
				'raw' => 'JSON_LENGTH(data) as completed',
				'sortField' => 'completed',
				'filter' => [
					'yes' => __('global.yes'),
					'no' => __('global.no')
				],
				'filterDefinitions' => [
					'yes' => ['=', function () {
						return Field::where('form', Worker::class)->count();
					}],
					'no' => ['<', function () {
						return Field::where('form', Worker::class)->count();
					}],
				],
				'title' => __('admin/workers.completed'),
				'callback' => 'dataCompleted|' . Worker::class
			], [
				'name' => 'count(file)',
				'sortField' => 'count(file)',
				'title' => __('global.photos'),
				'filter' => [
					'yes' => __('global.yes'),
					'no' => __('global.no')
				],
				'filterDefinitions' => [
					'yes' => ['>', 0],
					'no' => ['=', 0],
				],
				'callback' => 'numToBoolTag'
			], [
				'name' => 'approved',
				'sortField' => 'approved',
				'title' => __('admin/workers.approved'),
				'callback' => 'boolean',
				'filter' => [
					'1' => __('global.yes'),
					'0' => __('global.no')
				]
			], [
				'name' => 'photoList',
				'noTable' => true,
				'visible' => false
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
			], [
				'name' => 'date',
				'title' => __('admin/shifts.date'),
				'sortField' => 'date',
				'callback' => 'date'
			], [
				'name' => 'hours',
				'title' => __('admin/shifts.hours'),
				'sortField' => 'hours',
				'callback' => 'localNumber',
				'filter' => false,
			], [
				'name' => 'closed',
				'visible' => false,
			]],
		
		];
	}
}
