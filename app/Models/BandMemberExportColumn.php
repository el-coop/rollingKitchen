<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandMemberExportColumn extends Model {
	static public function options() {
		$options = collect([
			'user.name' => __('global.name'),
			'user.email' => __('global.email'),
			'bandMember.payment' => __('admin/workers.totalPayment'),
			'band.name' => __('admin/fields.Band')
		]);
		Field::where('form', BandMember::class)->get()->each(function ($bandColumn) use ($options) {
			$options->put(
				'bandMember.' . $bandColumn->id, $bandColumn->name_nl);
		});
		return $options;
	}
}
