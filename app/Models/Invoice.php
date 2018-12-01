<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
	
	static function getNumber() {
		$year = app('settings')->get('registration_year');
		$number = static::where('prefix', $year)->count() + 1;
		return "{$year}-{$number}";
	}
	
	public function getFullDataAttribute() {
		$language = $this->application->kitchen->user->language;
		
		$pdfs = Pdf::all()->pluck('name', 'id');
		
		
		return [[
			'name' => 'recipients',
			'label' => __('admin/invoices.recipients'),
			'type' => 'text',
			'value' => $this->application->kitchen->user->email,
		], [
			'name' => 'accountant',
			'label' => false,
			'type' => 'checkbox',
			'options' => [
				'true' => __('admin/invoices.accountant')
			],
		], [
			'name' => 'message',
			'label' => __('admin/invoices.message'),
			'type' => 'textarea',
			'value' => app('settings')->get("invoice_email_text_{$language}", ''),
		], [
			'name' => 'attachments',
			'label' => __('admin/invoices.attachments'),
			'type' => 'checkbox',
			'options' => $pdfs
		], [
			'name' => 'items',
			'label' => 'Items',
			'type' => 'invoice',
			'value' => $this->getOutstandingItems($language),
			'options' => $this->getOptions($language)
		], [
			'name' => 'file_download',
			'label' => __('admin/invoices.preview'),
			'type' => 'alternative-submit',
			'value' => true
		]];
	}
	
	public function application() {
		return $this->belongsTo(Application::class);
	}
	
	public function services() {
		return $this->belongsToMany(Service::class)->withPivot('quantity')->withTimestamps();
	}
	
	protected function getOptions($language) {
		$result = collect([[
			'name' => __('admin/invoices.fee', [], $language),
			'unitPrice' => $this->application->data['Fee (excl. VAT) (minimum of € 1250,-)']
		]]);
		$result = $result->concat(Service::all()->map(function ($service) use ($language) {
			return [
				'name' => $service->{"name_{$language}"},
				'unitPrice' => $service->price
			];
		}));
		
		return $result;
	}
	
	protected function getOutstandingItems($language) {
		$result = [];
		if (!$this->application->invoices()->count()) {
			$result[] = [
				'quantity' => 1,
				'item' => __('admin/invoices.fee', [], $language),
				'unitPrice' => $this->application->data['Fee (excl. VAT) (minimum of € 1250,-)']
			];
		}
		$invoicedServices = $this->application->invoicedServices();
		foreach ($this->application->services as $service) {
			$quanity = $service->pivot->quantity;
			$paidFor = $invoicedServices->sum("{$service->id}.price");
			$quanity -= $paidFor;
			if ($quanity > 0) {
				$result[] = [
					'quantity' => $quanity,
					'item' => $service->{"name_{$language}"},
					'unitPrice' => $service->price
				];
			}
		}
		return $result;
	}
}
