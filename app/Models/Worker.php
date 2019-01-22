<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;


class Worker extends Model {
	use HasFields;
	
	protected $casts = [
		'data' => 'array'
	];
	
	static function indexPage() {
		return action('Admin\KitchenController@index', [], false);
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
				'value' => $this->name
			], [
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->email
			], [
				'name' => 'type',
				'label' => __('admin/workers.type'),
				'type' => 'select',
				'options' => [
					__('admin/workers.payroll'),
					__('admin/workers.freelance'),
					__('admin/workers.volunteer'),
				],
				'value' => $this->type
			], [
				'name' => 'language',
				'label' => __('global.language'),
				'type' => 'select',
				'options' => [
					'nl' => __('global.nl'),
					'en' => __('global.en'),
				],
				'value' => $this->language ?? 'nl'
			], [
				'name' => 'workplaces',
				'type' => 'multiselect',
				'label' => __('admin/workers.workplaces'),
				'options' => Workplace::select('name', 'id')->get(),
				'optionsLabel' => 'name'
			], [
				'name' => 'Supervisor',
				'type' => 'Checkbox',
				'value' => $this->supervisor,
				'options' => [[
					'name' => __('admin/workers.supervisor')
				]]
			]
		]);
		
		return $fullData;
	}
	
	public function workplaces() {
		return $this->belongsToMany(Worker::class)->withPivot('function');
	}
}

