<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model {
	
	protected $casts = [
		'options' => 'array'
	];
	
	static function getRequiredFields(...$models) {
		$requiredFields = Field::where('status', 'required')->whereIn('form', $models)->orWhere('status', 'encrypted')->whereIn('form', $models)->get();
		return $requiredFields->mapWithKeys(function ($field) use (&$rules) {
			$dataName = strtolower(substr($field->form, strrpos($field->form, '\\') + 1));
			return ["{$dataName}.{$field->id}" => 'required'];
		});
	}
	
}
	
