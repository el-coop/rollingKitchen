<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model {
	
	use HasFields;
	
	protected $casts = [
		'data' => 'array'
	];
	
	static function indexPage() {
		return action('Admin\KitchenController@index', [], false);
	}
	
	public function showPage() {
		return action('Admin\KitchenController@view', $this);
	}
	
	public function homePage() {
		return action('Kitchen\KitchenController@show', $this);
	}
	
	public function user() {
		return $this->morphOne(User::class, 'user');
	}
	
	public function photos() {
		return $this->hasMany(Photo::class);
	}
	
	public function applications() {
		return $this->hasMany(Application::class);
	}
	
	public function getFullDataAttribute() {
		$fullData = collect([
			[
				'name' => 'name',
				'label' => __('misc.name'),
				'type' => 'text',
				'value' => $this->user->name
			], [
				'name' => 'email',
				'label' => __('misc.email'),
				'type' => 'text',
				'value' => $this->user->email
			], [
				'name' => 'status',
				'label' => __('misc.status'),
				'type' => 'select',
				'options' => [
					'new' => __('datatable.new'),
					'motherlist' => __('datatable.motherlist')
				],
				'value' => $this->status
			]
		]);
		
		return $fullData->concat($this->getFieldsData());
	}
	
	public function getCurrentApplication() {
		$application = $this->applications()->where('year', '2018')->first();
		if (!$application) {
			$application = new Application;
			$application->status = 'new';
			$application->year = 2018;
			$this->applications()->save($application);
		}
		return $application;
	}
}
