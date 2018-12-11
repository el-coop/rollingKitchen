<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model {
	use HasFields;
	
	static $fieldClass = Kitchen::class;
	
	protected $casts = [
		'data' => 'array'
	];
	
	public function getFullDataAttribute() {
		$fullData = collect([
			[
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->name
			], [
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->email
			], [
				'name' => 'language',
				'label' => __('global.language'),
				'type' => 'select',
				'options' => [
					'en' => __('global.en'),
					'nl' => __('global.nl'),
				],
				'value' => $this->language
			]
		]);
		
		return $fullData->concat($this->getFieldsData());
	}
}
