<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;


class Worker extends Model {
	use HasFields;

	protected $appends = [
		'workplacesList',
	];

	protected $casts = [
		'data' => 'array',
	];

	static function indexPage() {
		return action('Admin\KitchenController@index', [], false);
	}

	public function homePage() {
		return action('Worker\WorkerController@index', $this);
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
				'name' => 'type',
				'label' => __('admin/workers.type'),
				'type' => 'select',
				'options' => [
					__('admin/workers.payroll'),
					__('admin/workers.freelance'),
					__('admin/workers.volunteer'),
				],
				'value' => $this->type,
			], [
				'name' => 'workplaces',
				'type' => 'multiselect',
				'label' => __('admin/workers.workplaces'),
				'options' => Workplace::select('name', 'id')->get(),
				'optionsLabel' => 'name',
				'value' => $this->workplaces()->select('name','workplaces.id')->get(),
			], [
				'name' => 'Supervisor',
				'type' => 'Checkbox',
				'value' => $this->supervisor,
				'options' => [[
					'name' => __('admin/workers.makeSupervisor'),
				]],
			],
		]);

		if ($this->exists) {
			$fullData = $fullData->concat($this->getFieldsData());
		}

		return $fullData;
	}

	public function workplaces() {
		return $this->belongsToMany(Workplace::class)->withTimestamps();
	}

	public function getWorkplacesListAttribute() {
		return $this->workplaces->implode('name', ', ');

	}

	public function photos() {
		return $this->hasMany(WorkerPhoto::class);
	}

	public function isSupervisor(){
		return $this->supervisor;
	}
}

