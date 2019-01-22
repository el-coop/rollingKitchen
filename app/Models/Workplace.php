<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workplace extends Model {
	
	public function workFunctions() {
		return $this->hasMany(WorkFunction::class);
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
		return $this->belongsToMany(Worker::class)->withPivot('function');
	}
}
