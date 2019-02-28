<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model {
	
	public function getFullDataAttribute() {
		return [[
			'name' => 'name',
			'label' => __('global.name'),
			'type' => 'text',
			'value' => $this->name
		]];
	}
	
}
