<?php
/**
 * Created by PhpStorm.
 * User: lcd34
 * Date: 02/12/2018
 * Time: 12:08
 */

namespace App\Services;

use App\Models\Application;
use App\Models\Service;
use App\Services\IndividualTaxInvoice as InvoiceFile;
use Carbon\Carbon;
use DB;


class InvoiceService {
	
	protected $application;
	protected $recipient;
	protected $language;
	
	public function __construct($recipient) {
		$this->recipient = $recipient;
		
		if ($recipient instanceof Application) {
			$this->application = $recipient;
			$this->recipient = $recipient->kitchen->user;
			$this->recipient->data = $recipient->kitchen->data;
		} else {
			$this->recipient = $recipient;
		}
		$this->language = $this->recipient->language;
	}
	
	
	public function generate($number, $items, $tax = null, $date = null) {
		$settings = app('settings');
		
		$invoice = InvoiceFile::make()
			->date($date ?? Carbon::now())
			->language($this->language)
			->logo(asset('/images/logo.png'))
			->number($number)
			->taxType($tax !== null ? 'percentage' : 'individual')
			->tax($tax)
			->notes($settings->get("invoices_notes_{$this->language}"))
			->business(str_replace(PHP_EOL, '<br>', $settings->get("invoices_business_details")))
			->notes(str_replace(PHP_EOL, '<br>', $settings->get("invoices_notes_{$this->language}")))
			->footnote(str_replace(PHP_EOL, '<br>', $settings->get("invoices_footer_{$this->language}")))
			->customer([
				'name' => $this->recipient->name,
				'phone' => $this->recipient->data[5],
				'location' => $this->recipient->data[2],
				'zip' => $this->recipient->data[3],
				'city' => $this->recipient->data[4],
			]);
		
		foreach ($items as $item) {
			if (is_array($item)) {
				$invoice->addItem($item['item'], $item['unitPrice'], $item['quantity'], $item['tax'] ?? 0);
			} else {
				$invoice->addItem($item->name, $item->unit_price, $item->quantity, $item->tax ?? 0);
			}
		}
		return $invoice;
	}
	
	public function getOptions() {
		$result = Service::all()->map(function ($service) {
			return [
				'item' => $service->{"name_{$this->language}"},
				'unitPrice' => $service->price
			];
		});
		
		$result = $result->concat($this->getApplicationData());
		return $result;
	}
	
	public function getOutstandingItems() {
		$result = [];
		if (!$this->application->invoices()->count()) {
			$result = $this->getApplicationData();
			
		}
		$invoicedServices = $this->application->invoicedItems()->select('service_id', DB::raw('SUM(quantity) as quantity'))->where('service_id', '!=', null)->groupBy('service_id')->get();
		foreach ($this->application->services as $service) {
			$quanity = $service->pivot->quantity;
			$paidFor = $invoicedServices->firstWhere('service_id', $service->id)->quantity ?? 0;
			$quanity -= $paidFor;
			if ($quanity > 0) {
				$result[] = [
					'quantity' => $quanity,
					'item' => $service->{"name_{$this->language}"},
					'unitPrice' => $service->price
				];
			}
		}
		return $result;
	}
	
	protected function getApplicationData(): array {
		return [[
			'quantity' => 1,
			'item' => __('admin/invoices.fee', [], $this->language),
			'unitPrice' => $this->application->data[8]
		], [
			'quantity' => 1,
			'item' => __('kitchen/services.trash', [], $this->language),
			'unitPrice' => app('settings')->get('application_waste_processing_fee')
		]];
	}
}
