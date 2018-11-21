<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model {
	
	public $appends = [
		'url'
	];
	
	public function kitchen() {
		return $this->belongsTo(Kitchen::class);
	}
	
	public function getUrlAttribute() {
		return action('PhotoController@show', $this);
	}
}
