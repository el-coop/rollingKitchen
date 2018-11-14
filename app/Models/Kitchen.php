<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model {
	
	use HasFields;
	
	protected $casts = [
		'data' => 'array'
	];
	
	
	public function user() {
		return $this->morphOne(User::class, 'user');
	}
	
	public function photos() {
		return $this->hasMany(Photo::class);
	}
	
	public function getFullDataAttribute() {
		$fullData = collect([[
			'name' => 'name',
			'label' => __('misc.name'),
			'type' => 'text',
			'value' => $this->user->name
		], [
			'name' => 'email',
			'label' => __('misc.email'),
			'type' => 'text',
			'value' => $this->user->email
		], [
			'name' => 'status',
			'label' => __('misc.status'),
			'type' => 'select',
			'options' => [
				'new' => __('datatable.new'),
				'motherlist' => __('datatable.motherlist')
			],
			'value' => $this->status
		]]);
		
		$data = static::fields()->map(function ($item) {
			return [
				'name' => $item->name,
				'label' => $item->name,
				'type' => $item->type,
				'value' => $this->data[$item->name] ?? ''
			];
		});
		
		return $fullData->concat($data);
	}
}
