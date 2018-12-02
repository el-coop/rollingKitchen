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
		$kitchen = $this->application->kitchen;
		$invoice = InvoiceFile::make()
			->logo(asset('/images/logo.png'))
			->number($number)
			->tax($tax)
			->notes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
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
				$invoice->addItem($item->item, $item->unitPrice, $item->quantity);
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
		
		$result = $result->concat([$this->getApplicationData()]);
		
		if ($this->application->socket) {
			$result = $result->concat([$this->getSocketData()]);
		}
		return $result;
	}
	
	public function getOutstandingItems() {
		$result = [];
		if (!$this->application->invoices()->count()) {
			$result[] = $this->getApplicationData();
			if ($this->application->socket) {
				$result[] = $this->getSocketData();
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
					'item' => $service->{"name_{$this->language}"},
					'unitPrice' => $service->price
				];
			}
		}
		return $result;
	}
	
	protected function getApplicationData(): array {
		return [
			'quantity' => 1,
			'item' => __('admin/invoices.fee', [], $this->language),
			'unitPrice' => $this->application->data[8]
		];
	}
	
	protected function getSocketData() {
		
		switch ($this->application->socket) {
			case 1:
				$data = __('kitchen/services.2X230', [], $this->language);
				break;
			case 2:
				$data = __('kitchen/services.3x230', [], $this->language);
				break;
			case 3:
				$data = __('kitchen/services.1x400-16', [], $this->language);
				break;
			case 4:
				$data = __('kitchen/services.1x400-32', [], $this->language);
				break;
			default:
				$data = __('kitchen/services.2x400', [], $this->language);
		}
		
		$data = explode('€', $data);
		
		return [
			'quantity' => 1,
			'item' => trim($data[0]),
			'unitPrice' => trim($data[1])
		];
		
	}
	
}