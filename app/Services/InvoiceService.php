<?php
/**
 * Created by PhpStorm.
 * User: lcd34
 * Date: 02/12/2018
 * Time: 12:08
 */

namespace App\Services;

use App\Models\Service;
use ConsoleTVs\Invoices\Classes\Invoice as InvoiceFile;
use DB;


class InvoiceService {
	
	protected $application;
	protected $language;
	
	public function __construct($application) {
		$this->application = $application;
		$this->language = $application->kitchen->user->language;
	}
	
	
	public function generate($number, $tax, $items) {
		$settings = app('settings');
		$kitchen = $this->application->kitchen;
		$language = $kitchen->user->language;
		$invoice = InvoiceFile::make()
			->logo(asset('/images/logo.png'))
			->number($number)
			->tax($tax)
			->notes($settings->get("invoices_notes_{$language}"))
			->business(str_replace(PHP_EOL, '<br>', $settings->get("invoices_business_details")))
			->notes(str_replace(PHP_EOL, '<br>', $settings->get("invoices_notes_{$language}")))
			->footnote(str_replace(PHP_EOL, '<br>', $settings->get("invoices_footer_{$language}")))
			->customer([
				'name' => $kitchen->user->name,
				'phone' => $kitchen->data[5],
				'location' => $kitchen->data[2],
				'zip' => $kitchen->data[3],
				'city' => $kitchen->data[4],
			]);
		
		foreach ($items as $item) {
			if (is_array($item)) {
				$invoice->addItem($item['item'], $item['unitPrice'], $item['quantity']);
			} else {
				$invoice->addItem($item->name, $item->unit_price, $item->quantity);
			}
		}
		return $invoice;
	}
	
	public function getOptions() {
		$result = Service::where('category', '!=', 'socket')->get()->map(function ($service) {
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
			'unitPrice' => 50
		]];
	}
}