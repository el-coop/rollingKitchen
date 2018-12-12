<?php

namespace Tests\Feature\Admin\Debtors;

use App\Models\Admin;
use App\Models\Debtor;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoicingTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	private $user;
	private $kitchen;
	private $invoice;
	private $invoices;
	private $debtor;
	
	protected function setUp() {
		parent::setUp();
		$this->user = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->user);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create([
			'data' => [
				2 => 'test',
				3 => 'test',
				4 => 'test',
				5 => 'test',
			]
		])->user()->save($this->kitchen);
		$this->debtor = factory(Debtor::class)->make();
		
		$this->invoices = factory(Invoice::class, 4)->make();
		$this->invoices->each(function ($invoice) {
			$this->debtor->invoices()->save($invoice);
			$invoiceItems = rand(1, 4);
			$total = 0;
			for ($j = 0; $j < $invoiceItems; $j++) {
				$invoiceItem = factory(\App\Models\InvoiceItem::class)->make([
					'tax' => $this->faker->randomElement([0, 6, 21])
				]);
				$invoice->items()->save($invoiceItem);
				$total = $invoiceItem->unit_price * $invoiceItem->quantity * (1 + $invoiceItem->tax / 100);
			}
			
			
			$invoice->amount = $total;
			$invoice->save();
			
		});
		
	}
	
}
