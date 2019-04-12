<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenExportColumn extends Model {
	static public function options() {
		$options = collect([
			'user.name' => __('global.name'),
			'user.email' => __('global.email'),
			'application.number' => __('admin/applications.number'),
			'application.length' => __('kitchen/dimensions.length'),
			'application.width' => __('kitchen/dimensions.width'),
			'application.terrace_length' => __('kitchen/dimensions.terraceLength'),
			'application.terrace_width' => __('kitchen/dimensions.terraceWidth'),
			'application.year' => __('kitchen/kitchen.application') . ' ' .  __('global.year'),
		]);
		Field::where('form', Application::class)->get()->each(function ($applicationColumn) use ($options){
			$options->put('application.' . $applicationColumn->id, $applicationColumn->name_nl);
		});
		Service::all()->each(function ($service) use ($options){
			$options->put('service.' . $service->id, $service->name_nl);
		});
		Field::where('form', Kitchen::class)->get()->each(function ($kitchenColumn) use ($options) {
			$options->put(
				'kitchen.' . $kitchenColumn->id, $kitchenColumn->name_nl);
		});
		return $options;
	}
}
