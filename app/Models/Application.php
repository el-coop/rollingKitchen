<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Application extends Model {
	use HasFactory;
	use HasFields;

	protected static function boot() {
		parent::boot();
		static::deleted(function ($application) {
			$application->services()->detach();
			$application->products()->delete();
			$application->electricDevices()->delete();
		});
	}

	protected $casts = [
		'data' => 'array'
	];


	static function indexPage() {
		return action('Admin\ApplicationController@index', [], false);
	}

	public function kitchen() {
		return $this->belongsTo(Kitchen::class);
	}

	public function products() {
		return $this->hasMany(Product::class);
	}

	public function getFullDataAttribute() {
		$editData = collect([
			[
				'name' => 'year',
				'label' => __('global.year'),
				'type' => 'text',
				'value' => $this->year
			],
			[
				'name' => 'status',
				'label' => __('global.status'),
				'type' => 'select',
				'options' => [

					'pending' => __('vue.pending'),
					'accepted' => __('vue.accepted'),
					'reopened' => __('vue.reopened'),
					'rejected' => __('vue.rejected'),
					'backup' => __('vue.backup')
				],
				'value' => $this->status
			]
		]);

		return $editData->concat($this->getFieldsData());
	}

	public function services() {
		return $this->belongsToMany(Service::class)->withPivot('quantity')->withTimestamps();
	}

	public function invoices() {
		return $this->morphMany(Invoice::class, 'owner');
	}

	public function invoicedItems() {
		return $this->hasManyThrough(InvoiceItem::class, Invoice::class, 'owner_id')
			->where('owner_type', static::class);;
	}

	public function hasService(Service $service) {

		return !!$this->services->contains(function ($applicationService) use ($service) {
			return $applicationService->id == $service->id && $applicationService->pivot->quantity > 0;
		});
	}

	public function serviceQuantity(Service $service) {
		return $this->services->firstWhere('id', $service->id)->pivot->quantity ?? 0;
	}

	public function electricDevices() {
		return $this->hasMany(ElectricDevice::class);
	}

	public function isOpen() {
		$settings = app('settings');
		return ($this->status == 'new' || $this->status == 'reopened') && $this->year == $settings->get('registration_year') && $settings->get('general_registration_status');
	}

	public function setNumber() {
		$this->number = (static::where('year', $this->year)->max('number') ?? 0) + 1;
		$this->save();
	}

	public function registerNewServices(Service $service) {
		$paidQuantity = $this->invoicedItems()->select(DB::raw('SUM(quantity) as quantity'))->where('service_id', '=', $service->id)
			->groupBy('service_id')->first()->quantity;

		$requestedQuantity = $this->serviceQuantity($service);
		if ($paidQuantity < 1) {
			$this->services()->detach($service->id);
		} else if (!$requestedQuantity || $paidQuantity != $requestedQuantity) {
			$this->services()->syncWithoutDetaching([$service->id => [
				'quantity' => $paidQuantity
			]]);
		}
	}

	public function hasMenu() {

		return $this->products()->where('category', 'menu')->exists();

	}

    public function sketches(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(ApplicationSketch::class);
    }
}
