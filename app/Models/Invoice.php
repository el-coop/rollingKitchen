<?php

namespace App\Models;

use App\Services\InvoiceService;
use DB;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Invoice extends Model {
	
	protected $appends = [
		'total',
		'taxAmount',
		'formattedNumber',
		'totalPaid'
	];
	
	protected static function boot() {
		parent::boot();
		static::deleted(function ($invoice) {
			$invoice->items->each->delete();
		});
	}

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
	
	public function getFormattedNumberAttribute() {
		$padding = '';
		if (strlen($this->number) == 1) {
			$padding = '00';
		} else if (strlen($this->number) == 2) {
			$padding = '0';
		}
		
		return "{$this->prefix}-{$padding}{$this->number}";
	}
	
	public function getTaxAmountAttribute() {
		return $this->amount * $this->tax / 100;
	}
	
	public function getTotalAttribute() {
		return $this->amount + $this->taxAmount;
	}
	
	public function getFullDataAttribute() {
		$language = $this->owner instanceof Application ? $this->owner->kitchen->user->language : $this->owner->language;
		$settings = app('settings');

		$pdfs = Pdf::all()->pluck('name', 'id');

		
		$pdfs = collect([]);
		$options = collect([]);
		$items = $this->formattedItems;
		$subject = '';
		$message = '';
		$individualTax = true;
		$taxOptions = [
			'21' => '21%',
			'6' => '6%',
			'0' => '0',
		];
		if ($this->owner instanceof Application) {
			$taxOptions = [
				'21' => '21%',
				'0' => '0',
			];
			$individualTax = false;
			$invoiceService = new InvoiceService($this->owner);
			$options = $invoiceService->getOptions($language);
			$pdfs = Pdf::all()->pluck('name', 'id');
			if ($this->exists) {
				$subject = $settings->get("invoices_default_resend_subject_{$language}", '');
				$message = $settings->get("invoices_default_resend_email_{$language}", '');
			} else {
				$items = $invoiceService->getOutstandingItems($language);
				$subject = $settings->get("invoices_default_subject_{$language}", '');
				$message = $settings->get("invoices_default_email_{$language}", '');
			}
		}
		
		return [[
			'name' => 'recipient',
			'label' => __('admin/invoices.recipient'),
			'type' => 'text',
			'value' => $this->owner instanceof Application ? $this->owner->kitchen->user->email : $this->owner->email,
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
			'name' => 'subject',
			'label' => __('admin/invoices.subject'),
			'type' => 'text',
			'checked' => true,
			'value' => $subject,
		], [
			'name' => 'message',
			'label' => __('admin/invoices.message'),
			'type' => 'textarea',
			'value' => $message,
		], [
			'name' => 'attachments',
			'label' => __('admin/invoices.attachments'),
			'type' => 'checkbox',
			'options' => $pdfs
		], [
			'name' => 'items',
			'label' => 'Items',
			'type' => 'invoice',
			'value' => $items,
			'individualTax' => $individualTax,
			'options' => $options,
			'taxOptions' => $taxOptions
		], [
			'name' => 'file_download',
			'label' => __('admin/invoices.preview'),
			'type' => 'alternative-submit',
			'value' => true
		]];
	}
	
	public function owner() {
		return $this->morphTo();
	}
	
	public function items() {
		return $this->hasMany(InvoiceItem::class);
	}
	
	public function getFormattedItemsAttribute() {
		return $this->items->map(function ($item) {
			return [
				'quantity' => $item->quantity,
				'item' => $item->name,
				'tax' => $item->tax,
				'unitPrice' => $item->unit_price,
			];
		});
	}
	
	public function services() {
		return $this->hasManyThrough(Service::class, InvoiceItem::class);
	}

	public function payments() {
		return $this->hasMany(InvoicePayment::class);
	}

	public function getTotalPaidAttribute() {
		return $this->payments()->sum('amount');
	}
}
