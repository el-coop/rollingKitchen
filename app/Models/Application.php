<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Application extends Model {
	
	use HasFields;
	
	protected static function boot() {
		parent::boot();
		static::deleted(function ($application) {
			$application->services()->sync([]);
			$application->products->each->delete();
			$application->electricDevices->each->delete();
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
		return $this->hasMany(Invoice::class);
	}
	
	public function invoicedItems() {
		return $this->hasManyThrough(InvoiceItem::class, Invoice::class);
	}
	
	public function hasService(Service $service) {
		return $this->services()->where('service_id', $service->id)->where('quantity', '>', 0)->exists();
	}
	
	public function serviceQuantity(Service $service) {
		return $this->services()->where('service_id', $service->id)->first()->pivot->quantity ?? 0;
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
		if (!$requestedQuantity || $paidQuantity > $requestedQuantity) {
			$this->services()->syncWithoutDetaching([$service->id => [
				'quantity' => $paidQuantity
			]]);
		}
	}
}
