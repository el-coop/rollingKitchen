<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 08/11/2018
 * Time: 15:29
 */

namespace App\Models\Traits;


use App\Models\Field;

trait HasFields {
	static function fields() {
		return Field::where('form', static::class)->orderBy('order')->get();
	}
	
	static function getLastFieldOrder() {
		return Field::where('form', '=', static::class)->max('order');
	}
	
	public function getFieldsData() {
		return static::fields()->map(function ($item) {
			return [
				'name' => $item->name,
				'label' => $item->name,
				'type' => $item->type,
				'value' => $this->data[$item->name] ?? ''
			];
		});
	}
	
}