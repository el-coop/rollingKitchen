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
		return action('Kitchen\KitchenController@edit', $this);
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
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->user->name
			], [
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->user->email
			], [
				'name' => 'language',
				'label' => __('global.language'),
				'type' => 'select',
				'options' => [
					'en' => __('global.en'),
					'nl' => __('global.nl'),
				],
				'value' => $this->user->language
			], [
				'name' => 'status',
				'label' => __('global.status'),
				'type' => 'select',
				'options' => [
					'new' => __('admin/kitchens.new'),
					'motherlist' => __('admin/kitchens.motherlist')
				],
				'value' => $this->status
			]
		]);
		
		return $fullData->concat($this->getFieldsData());
	}
	
	public function getCurrentApplication() {
		$applicationYear = app('settings')->get('registration_year');
		$application = $this->applications()->where('year', $applicationYear)->first();
		if (!$application) {
			$application = new Application;
			$application->status = 'new';
			$application->year = $applicationYear;
			$application->data = [];
			$application->length = 0;
			$application->width = 0;
			$this->applications()->save($application);
		}
		return $application;
	}
	
}
