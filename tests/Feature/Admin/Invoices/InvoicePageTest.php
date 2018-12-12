<?php

namespace Tests\Feature\Admin\Invoices;

use App\Jobs\SendApplicationInvoice;
use App\Jobs\SendDebtorInvoice;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Debtor;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\User;
use Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoicePageTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	private $user;
	private $kitchen;
	private $debtor;
	private $debtorInvoices;
	private $application;
	private $applicationInvoices;
	
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
		$this->application = factory(Application::class)->make([
			'data' => [
				8 => '1000'
			]
		]);
		$this->kitchen->user->applications()->save($this->application);
		$this->debtor = factory(Debtor::class)->create();
		
		$this->debtorInvoices = factory(Invoice::class, 4)->make();
		$this->debtorInvoices->each(function ($invoice) {
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
		
		$this->applicationInvoices = factory(Invoice::class, 4)->make();
		$this->applicationInvoices->each(function ($invoice) {
			$this->application->invoices()->save($invoice);
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
	
	public function test_guest_cant_see_existing_debtor_invoice_form() {
		$this->get('/admin/invoices/debtor/' . $this->debtorInvoices->first()->id)->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_existing_debtor_invoice_form() {
		$this->actingAs($this->kitchen)->get('/admin/invoices/debtor/' . $this->debtorInvoices->first()->id)->assertForbidden();
	}
	
	public function test_admin_loads_existing_debtor_invoice_form() {
		$invoice = $this->debtorInvoices->random();
		
		$items = $invoice->items->map(function ($item) {
			return [
				'item' => $item->name,
				'quantity' => $item->quantity,
				'unitPrice' => $item->unit_price,
				'tax' => $item->tax,
			];
		});
		
		$response = $this->actingAs($this->user)->get('/admin/invoices/debtor/' . $invoice->id)
			->assertSuccessful()->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->debtor->email,
			])->assertJsonFragment([[
				'name' => 'subject',
				'label' => __('admin/invoices.subject'),
				'type' => 'text',
				'checked' => true,
				'value' => '',
			]])->assertJsonFragment([
				'name' => 'message',
				'label' => __('admin/invoices.message'),
				'type' => 'textarea',
				'value' => '',
			])->assertJsonFragment([
				'name' => 'items',
				'label' => 'Items',
				'type' => 'invoice',
				'taxOptions' => [
					'21' => '21%',
					'6' => '6%',
					'0' => '0',
				]]);
		
		
		foreach ($items as $item) {
			$response->assertJsonFragment($item);
		}
	}
	
	public function test_guest_cant_see_existing_application_invoice_form() {
		$this->get('/admin/invoices/application/' . $this->applicationInvoices->first()->id)->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_existing_application_invoice_form() {
		$this->actingAs($this->kitchen)->get('/admin/invoices/application/' . $this->applicationInvoices->first()->id)->assertForbidden();
	}
	
	public function test_admin_loads_existing_application_invoice_form() {
		$settings = app('settings');
		$language = $this->kitchen->language;
		
		$invoice = $this->applicationInvoices->random();
		
		$items = $invoice->items->map(function ($item) {
			return [
				'item' => $item->name,
				'quantity' => $item->quantity,
				'unitPrice' => $item->unit_price,
				'tax' => $item->tax,
			];
		});
		
		$response = $this->actingAs($this->user)->get('/admin/invoices/application/' . $invoice->id)
			->assertSuccessful()->assertSuccessful()->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->kitchen->email,
			])->assertJsonFragment([[
				'name' => 'subject',
				'label' => __('admin/invoices.subject'),
				'type' => 'text',
				'checked' => true,
				'value' => $settings->get("invoices_default_resend_subject_{$language}", ''),
			]])->assertJsonFragment([
				'name' => 'message',
				'label' => __('admin/invoices.message'),
				'type' => 'textarea',
				'value' => $settings->get("invoices_default_resend_email_{$language}", ''),
			])->assertJsonFragment([
				'name' => 'items',
				'label' => 'Items',
				'type' => 'invoice',
				'taxOptions' => [
					'21' => '21%',
					'0' => '0',
				]]);
		
		foreach ($items as $item) {
			$response->assertJsonFragment($item);
		}
	}
	
	public function test_guest_cant_edit_debtor_invoice() {
		Queue::fake();
		$invoice = $this->debtorInvoices->first();
		
		$this->patch('/admin/invoices/debtor/' . $invoice->id, [
			'tax' => 21,
			'recipient' => $this->debtor->email,
			'bcc' => $this->debtor->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'item' => 'test2'
			]]
		])->assertRedirect(action('Auth\LoginController@login'));
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
	}
	
	public function test_kitchen_cant_edit_debtor_invoice() {
		Queue::fake();
		$invoice = $this->debtorInvoices->first();
		
		$this->actingAs($this->kitchen)->patch('/admin/invoices/debtor/' . $invoice->id, [
			'tax' => 21,
			'recipient' => $this->debtor->email,
			'bcc' => $this->debtor->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'item' => 'test2'
			]]
		])->assertForbidden();
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
	}
	
	
	public function test_admin_can_edit_debtor_invoice() {
		
		Queue::fake();
		$invoice = $this->debtorInvoices->random();
		
		$prefix = app('settings')->get('registration_year');
		
		$this->actingAs($this->user)->patch('/admin/invoices/debtor/' . $invoice->id, [
			'recipient' => $this->debtor->email,
			'bcc' => $this->debtor->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'tax' => 0,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'tax' => 21,
				'item' => 'test2'
			]]
		])->assertSuccessful()->assertJson([
			'prefix' => $prefix,
			'number' => $invoice->number,
			'tax' => 0,
			'owner_id' => $this->debtor->id,
			'owner_type' => Debtor::class,
			'amount' => 5.84,
			'total' => 5.84,
			'taxAmount' => 0,
		]);
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 1,
			'unit_price' => 1,
			'tax' => 0,
			'name' => 'test',
			'invoice_id' => $invoice->id
		]);
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 2,
			'unit_price' => 2,
			'tax' => 21,
			'name' => 'test2',
			'invoice_id' => $invoice->id
		]);
		
		$this->assertDatabaseHas('invoices', [
			'id' => $invoice->id,
			'number' => $invoice->number,
			'tax' => 0,
			'amount' => 5.84,
		]);
		$this->assertCount(2, $invoice->items);
		Queue::assertPushed(SendDebtorInvoice::class);
	}
	
	public function test_edit_debtor_invoice_validation() {
		
		Queue::fake();
		$invoice = $this->debtorInvoices->first();
		
		$this->actingAs($this->user)->patch('/admin/invoices/debtor/' . $invoice->id, [
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
		
	}
	
	public function test_guest_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->applicationInvoices->first();
		
		$this->patch('/admin/invoices/application/' . $invoice->id, [
			'tax' => 21,
			'recipient' => $this->kitchen->email,
			'bcc' => $this->user->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'item' => 'test2'
			]]
		])->assertRedirect(action('Auth\LoginController@login'));
		
		Queue::assertNotPushed(SendApplicationInvoice::class);
	}
	
	public function test_kitchen_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->applicationInvoices->first();
		
		$this->actingAs($this->kitchen)->patch('/admin/invoices/application/' . $invoice->id, [
			'tax' => 21,
			'recipient' => $this->kitchen->email,
			'bcc' => $this->user->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'item' => 'test2'
			]]
		])->assertForbidden();
		
		Queue::assertNotPushed(SendApplicationInvoice::class);
	}
	
	
	public function test_admin_can_edit_invoice() {
		
		Queue::fake();
		$invoice = $this->applicationInvoices->first();
		
		$prefix = app('settings')->get('registration_year');
		
		$this->actingAs($this->user)->patch('/admin/invoices/application/' . $invoice->id, [
			'tax' => 21,
			'recipient' => $this->kitchen->email,
			'bcc' => $this->user->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'item' => 'test2'
			]]
		])->assertSuccessful()->assertJson([
			'prefix' => $prefix,
			'number' => $invoice->number,
			'tax' => 21,
			'owner_id' => 1,
			'owner_type' => Application::class,
			'amount' => 5,
			'total' => 6.05,
			'taxAmount' => 1.05,
		]);
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 1,
			'unit_price' => 1,
			'name' => 'test',
			'invoice_id' => $invoice->id
		]);
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 2,
			'unit_price' => 2,
			'name' => 'test2',
			'invoice_id' => $invoice->id
		]);
		
		$this->assertDatabaseHas('invoices', [
			'id' => $invoice->id,
			'number' => $invoice->number,
			'tax' => 21,
			'amount' => 5,
		]);
		$this->assertCount(2, $invoice->items);
		Queue::assertPushed(SendApplicationInvoice::class);
		
	}
	
	public function test_edit_application_invoice_validation() {
		
		Queue::fake();
		$invoice = $this->applicationInvoices->first();
		
		$this->actingAs($this->user)->patch('/admin/invoices/application/' . $invoice->id, [
			'tax' => '',
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['tax', 'recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendApplicationInvoice::class);
		
	}
}
