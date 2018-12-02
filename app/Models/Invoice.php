<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Invoice extends Model {
	
	protected $appends = [
		'total',
		'taxAmount'
	];
	
	protected $casts = [
		'paid' => 'boolean'
	];
	
	static function getNumber() {
		$year = app('settings')->get('registration_year');
		$number = static::where('prefix', $year)->count() + 1;
		$padding = '';
		if ($number < 100) {
			$padding .= 0;
		}
		if ($number < 10) {
			$padding .= 0;
		}
		return "{$padding}{$number}";
	}
	
	public function getTaxAmountAttribute() {
		return $this->amount * $this->tax / 100;
	}
	
	public function getTotalAttribute() {
		return $this->amount + $this->taxAmount;
	}
	
	public function getFullDataAttribute() {
		$language = $this->application->kitchen->user->language;
		
		$pdfs = Pdf::all()->pluck('name', 'id');
		
		
		return [[
			'name' => 'recipient',
			'label' => __('admin/invoices.recipient'),
			'type' => 'text',
			'value' => $this->application->kitchen->user->email,
		], [
			'name' => 'bcc',
			'label' => __('admin/invoices.bcc'),
			'type' => 'text',
			'value' => Auth::user()->email,
		], [
			'name' => 'accountant',
			'label' => false,
			'type' => 'checkbox',
			'checked' => true,
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
			'options' => $this->getOptions($language),
			'taxOptions' => [
				'21' => '21%',
				'0' => '0',
			]
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
	
	public function items() {
		return $this->hasMany(InvoiceItem::class);
	}
	
	protected function getOptions($language) {
		
		$result = Service::all()->map(function ($service) use ($language) {
			return [
				'name' => $service->{"name_{$language}"},
				'unitPrice' => $service->price
			];
		});
		
		$result = $result->concat([$this->getApplicationData($language)]);
		
		if ($this->application->socket) {
			$result = $result->concat([$this->getSocketData($this->application->socket, $language)]);
		}
		
		return $result;
	}
	
	protected function getOutstandingItems($language) {
		$result = [];
		if (!$this->application->invoices()->count()) {
			$result[] = $this->getApplicationData($language);
			if ($this->application->socket) {
				$result[] = $this->getSocketData($this->application->socket, $language);
			}
		}
		$invoicedServices = $this->application->invoicedItems()->select('service_id', DB::raw('COUNT(*) as quantity'))->where('service_id', '!=', null)->groupBy('service_id')->get();
		foreach ($this->application->services as $service) {
			$quanity = $service->pivot->quantity;
			$paidFor = $invoicedServices->firstWhere('service_id', $service->id)->quantity ?? 0;
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
	
	public function services() {
		return $this->hasManyThrough(Service::class, InvoiceItem::class);
	}
	
	protected function getSocketData($socket, $language) {
		
		$data = '';
		switch ($socket) {
			case 1:
				$data = __('kitchen/services.2X230', [], $language);
				break;
			case 2:
				$data = __('kitchen/services.3x230', [], $language);
				break;
			case 3:
				$data = __('kitchen/services.1x400-16', [], $language);
				break;
			case 4:
				$data = __('kitchen/services.1x400-32', [], $language);
				break;
			default:
				$data = __('kitchen/services.2x400', [], $language);
		}
		
		$data = explode('€', $data);
		
		return [
			'quantity' => 1,
			'item' => trim($data[0]),
			'unitPrice' => trim($data[1])
		];
		
	}
	
	/**
	 * @param $language
	 * @return array
	 */
	protected function getApplicationData($language): array {
		return [
			'quantity' => 1,
			'item' => __('admin/invoices.fee', [], $language),
			'unitPrice' => $this->application->data[8]
		];
	}
}
