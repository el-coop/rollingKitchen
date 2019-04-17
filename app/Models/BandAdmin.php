<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class BandAdmin extends Model {
	use HasFields;

	public function band() {
		return $this->belongsTo(Band::class);
	}

	static $fieldClass = BandMember::class;

	protected $casts = [
		'data' => 'array'
	];

	protected $appends = [
		'photoList'
	];

	public function photos() {
		return $this->hasMany(BandAdminPhoto::class);
	}

	public function getFullDataAttribute() {
		$fullData = collect([
			[
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->name ?? '',
			], [
				'name' => 'payment',
				'label' => __('band/band.payment'),
				'type' => 'text',
				'subType' => 'number',
				'value' => $this->payment ?? 0,

			]
		]);
		if ($this->exists) {
			$fullData = $fullData->concat($this->getFieldsData());
		}
		return $fullData;
	}

	public function getPhotoListAttribute() {
		return $this->photos;
	}
}
