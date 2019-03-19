<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Band extends Model {
	use HasFields;
	
	protected static function boot() {
		parent::boot(); // TODO: Change the autogenerated stub
		static::deleted(function ($band) {
			$band->user->delete();
		});
	}

	protected $casts = [
		'data' => 'array',
	];
	
	static function indexPage() {
		return action('Admin\BandController@index', [], false);
	}
	
	public function homePage() {
		return action('Band\BandController@show', $this);
	}
	
	public function user() {
		return $this->morphOne(User::class, 'user');
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
			],
			[
				'name' => 'paymentMethod',
				'label' => __('band/band.paymentMethod'),
				'type' => 'select',
				'options' => [
					'band' => __('admin/fields.Band'),
					'individual' => __('band/band.individual')
				],
				'value' => $this->payment_method ?? 'band'
			]
		]);
		if ($this->exists) {
			$fullData = $fullData->concat($this->getFieldsData());
		}
		return $fullData;
	}
	
	public function bandMembers() {
		return $this->hasMany(BandMember::class);
	}
	
	public function getBandMembersForTableAttribute() {
		return $this->bandMembers->map(function ($bandMember) {
			return [
				'id' => $bandMember->id,
				'name' => $bandMember->user->name,
				'email' => $bandMember->user->email,
				'language' => $bandMember->user->language
			];
		});
	}
	
	public function schedules() {
		return $this->hasMany(BandSchedule::class);
	}

	public function getBandMembersForDatatableAttribute() {
		return [
			'model' => BandMember::class,
			'where' => [['user_type', BandMember::class],['band_id', $this->id]],
			'joins' => [['users', 'users.user_id', 'band_members.id']],
			'fields' => [
				[
					'name' => 'id',
					'table' => 'band_members',
					'title' => 'id',
					'visible' => false
				], [
					'name' => 'name',
					'title' => __('global.name'),
					'sortField' => 'name',
				], [
					'name' => 'email',
					'title' => __('global.email'),
					'sortField' => 'email',
				]
			]
		];
	}

	public function getPendingScheduleAttribute(){
		return $this->schedules->where('approved', 'pending')->map(function ($schedule) {
			return [
				'id' => $schedule->id,
				'stage' => $schedule->stage->name,
				'dateTime' => $schedule->dateTime,
				'payment' => $schedule->payment
			];
		});
	}
}
