<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;



class Worker extends Model
{
	use HasFields;

	protected $casts = [
		'data' => 'array'
	];

	static function indexPage() {
		return action('Admin\KitchenController@index', [], false);
	}
	public function user() {
		return $this->morphOne(User::class, 'user');
	}

}

