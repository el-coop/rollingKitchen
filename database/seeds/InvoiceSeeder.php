<?php

use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		\App\Models\Application::all()->each(function ($application) {
			$invoices = rand(0, 4);
			for ($j = 0; $j < $invoices; $j++) {
				$invoice = factory(\App\Models\Invoice::class)->make();
				$application->invoices()->save($invoice);
				
				$invoiceItems = rand(1, 4);
				
				for ($j = 0; $j < $invoiceItems; $j++) {
					$invoiceItem = factory(\App\Models\InvoiceItem::class)->make();
					$invoice->items()->save($invoiceItem);
					$total = $invoiceItem->unit_price * $invoiceItem->quantity;
				}
				
				$invoice->amount = $total;
				$invoice->save();
				
			}
		});
	}
}
