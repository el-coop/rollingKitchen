<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenExportColumn extends Model {
	static public function options() {
		$options = collect([
			'user.name' => __('global.name'),
			'user.email' => __('global.email'),
			'services.services' => __('kitchen/kitchen.services'),
		]);
		Field::where('form', Kitchen::class)->get()->each(function ($kitchenColumn) use ($options) {
			$options->put(
				'band.' . $kitchenColumn->id, $kitchenColumn->name_nl);
		});
		return $options;
	}
}
