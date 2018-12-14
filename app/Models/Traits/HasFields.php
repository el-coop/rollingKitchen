<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 08/11/2018
 * Time: 15:29
 */

namespace App\Models\Traits;


use App;
use App\Models\Field;

trait HasFields {

	static function fields() {
		$field = property_exists(static::class, 'fieldClass') ? static::$fieldClass : static::class;
		return Field::where('form', $field)->orderBy('order')->get();
	}

	static function getLastFieldOrder() {
		$field = property_exists(static::class, 'fieldClass') ? static::$fieldClass : static::class;
		return Field::where('form', '=', $field)->max('order');
	}

	public function getFieldsData() {
		$field = property_exists(static::class, 'fieldClass') ? static::$fieldClass : static::class;


		$dataName = strtolower(substr($field, strrpos($field, '\\') + 1));

		return static::fields()->map(function ($item) use ($dataName) {
			return [
				'name' => "{$dataName}[{$item->id}]",
				'label' => $item->{'name_' . App::getLocale()},
				'type' => $item->type,
				'value' => $this->data[$item->id] ?? '',
				'placeholder' => $item->{'placeholder_' . App::getLocale()}
			];
		});
	}

}
