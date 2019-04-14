<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class BandMember extends Model {
	use HasFields;
	
	protected static function boot() {
		parent::boot();
		static::deleted(function ($bandMember) {
			$bandMember->user->delete();
		});
	}
	
	protected $casts = [
		'data' => 'array',
	];
	
	protected $appends = [
		'photoList'
	];
	
	static function indexPage() {
		return action('Admin\BandController@index', [], false);
	}
	
	public function homePage() {
		return action('BandMember\BandMemberController@show', $this);
	}
	
	public function user() {
		return $this->morphOne(User::class, 'user');
	}
	
	public function band() {
		return $this->belongsTo(Band::class);
	}
	
	public function getFullDataAttribute() {
		$fullData = collect([
			[
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->user->name ?? '',
			], [
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->user->email ?? '',
			], [
				'name' => 'language',
				'label' => __('global.language'),
				'type' => 'select',
				'options' => [
					'nl' => __('global.nl'),
					'en' => __('global.en'),
				],
				'value' => $this->user->language ?? 'nl',
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
	
	public function photos() {
		return $this->hasMany(BandMemberPhoto::class);
	}
	
	public function getPhotoListAttribute() {
		return $this->photos;
	}
}
