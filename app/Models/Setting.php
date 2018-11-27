<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
	
	public static function all($columns = ['*']) {
		$settings = parent::all($columns);
		$settings->push(Setting::registrationYear());
		return $settings;
	}
	
	public static function registrationYear() {
		if (today() > Carbon::create(date('Y'), 12, 15)) {
			$year = date('Y') + 1;
		} else {
			$year = date('Y');
		}
		$registrationYear = new Setting;
		$registrationYear->name = 'registration_year';
		$registrationYear->value = $year;
		return $registrationYear;
	}
}
