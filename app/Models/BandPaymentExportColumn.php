<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandPaymentExportColumn extends Model {
    use HasFactory;

    static public function options() {
		$options = collect([
			'user.name' => __('global.name'),
			'user.email' => __('global.email'),
			'band.totalPayment' => __('admin/workers.totalPayment'),
		]);
		Field::where('form', Band::class)->get()->each(function ($bandColumn) use ($options) {
			$options->put(
				'band.' . $bandColumn->id, $bandColumn->name_nl);
		});
		return $options;
	}
}
