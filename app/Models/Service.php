<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {
	public function getFullDataAttribute() {
		$fullData = collect([[
			'name' => 'name',
			'label' => __('misc.name'),
			'type' => 'text',
			'value' => $this->name,
		], [
			'name' => 'category',
			'label' => __('misc.category'),
			'type' => 'select',
			'options' => [
				'safety' => __('services.safety'),
				'electrical' => __('services.electrical'),
				'misc' => __('services.misc'),
			],
			'value' => $this->category,
		], [
			'name' => 'type',
			'label' => __('misc.type'),
			'type' => 'select',
			'options' => [
				__('services.amount'),
				__('services.select'),
			],
			'value' => $this->type,
		], [
			'name' => 'price',
			'label' => __('misc.price'),
			'type' => 'text',
			'value' => $this->price,
		]]);
		
		return $fullData;
	}
	
	public function applications() {
		return $this->belongsToMany(Application::class)->withPivot('quantity');
	}
}
