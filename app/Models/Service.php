<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {
	
	public function getFullDataAttribute() {
		$fullData = collect([[
			'name' => 'name_nl',
			'label' => __('admin/fields.name_nl'),
			'type' => 'text',
			'value' => $this->name_nl,
		], [
			'name' => 'name_en',
			'label' => __('admin/fields.name_en'),
			'type' => 'text',
			'value' => $this->name_en,
		], [
			'name' => 'category',
			'label' => __('admin/services.category'),
			'type' => 'select',
			'options' => [
				'safety' => __('vue.safety'),
				'electrical' => __('vue.electrical'),
				'misc' => __('vue.misc'),
			],
			'value' => $this->category,
		], [
			'name' => 'type',
			'label' => __('admin/fields.type'),
			'type' => 'select',
			'options' => [
				__('admin/services.amount'),
				__('admin/services.select'),
			],
			'value' => $this->type,
		], [
			'name' => 'price',
			'label' => __('admin/applications.price'),
			'type' => 'text',
			'subType' => 'number',
			'value' => $this->price,
		]]);
		return $fullData;
	}
	
	public function applications() {
		return $this->belongsToMany(Application::class)->withPivot('quantity')->withTimestamps();
	}
	
}
