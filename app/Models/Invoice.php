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
		$invoiceService = new InvoiceService($this->application);
		$language = $this->application->kitchen->user->language;
		$settings = app('settings');

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
			'name' => 'subject',
			'label' => __('admin/invoices.subject'),
			'type' => 'text',
			'checked' => true,
			'value' => $this->exists ? $settings->get("invoices_default_resend_subject_{$language}", '') : $settings->get("invoices_default_subject_{$language}", ''),
		], [
			'name' => 'message',
			'label' => __('admin/invoices.message'),
			'type' => 'textarea',
			'value' => $this->exists ? $settings->get("invoices_default_resend_email_{$language}", '') : $settings->get("invoices_default_email_{$language}", ''),
		], [
			'name' => 'attachments',
			'label' => __('admin/invoices.attachments'),
			'type' => 'checkbox',
			'options' => $pdfs
		], [
			'name' => 'items',
			'label' => 'Items',
			'type' => 'invoice',
			'value' => $this->items()->count() ? $this->formattedItems : $invoiceService->getOutstandingItems($language),
			'options' => $invoiceService->getOptions($language),
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


	public function getFormattedItemsAttribute() {
		return $this->items->map(function ($item) {
			return [
				'quantity' => $item->quantity,
				'item' => $item->name,
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
