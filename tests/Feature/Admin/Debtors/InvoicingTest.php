<?php

namespace Tests\Feature\Admin\Debtors;

use App\Jobs\SendApplicationInvoice;
use App\Jobs\SendDebtorInvoice;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Debtor;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoicingTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;

	protected $worker;
	private $user;
	private $kitchen;
	private $accountant;
	private $invoice;
	private $invoices;
	private $debtor;
	
	protected function setUp(): void {
		parent::setUp();
		$this->user = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->user);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create([
			'data' => [
				2 => 'test',
				3 => 'test',
				4 => 'test',
				5 => 'test',
			]
		])->user()->save($this->kitchen);
		$this->debtor = factory(Debtor::class)->create();
		
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
	
	public function test_guest_cant_see_new_invoice_form() {
		$this->get(action('Admin\DebtorInvoiceController@create', $this->debtor))
			->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_new_invoice_form() {
		$this->actingAs($this->worker)->get(action('Admin\DebtorInvoiceController@create', $this->debtor))
			->assertForbidden();
	}

	public function test_accountant_cant_see_new_invoice_form() {
		$this->actingAs($this->accountant)->get(action('Admin\DebtorInvoiceController@create', $this->debtor))
			->assertForbidden();
	}
	
	public function test_kitchen_cant_see_new_invoice_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\DebtorInvoiceController@create', $this->debtor))
			->assertForbidden();
	}
	
	public function test_admin_loads_new_invoice_form() {
		
		$this->actingAs($this->user)->get(action('Admin\DebtorInvoiceController@create', $this->debtor))
			->assertSuccessful()->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->debtor->email,
			])
			->assertJsonFragment([[
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
				'value' => [],
				'taxOptions' => [
					'21' => '21%',
					'6' => '6%',
					'0' => '0',
				]]);
		
	}
	
	public function test_guest_cant_create_new_invoice() {
		Queue::fake();
		
		$this->post(action('Admin\DebtorController@store', $this->debtor), [
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

	public function test_worker_cant_create_new_invoice() {
		Queue::fake();

		$this->actingAs($this->worker)->post(action('Admin\DebtorController@store', $this->debtor), [
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

	public function test_kitchen_cant_create_new_invoice() {
		Queue::fake();
		
		$this->actingAs($this->kitchen)->post(action('Admin\DebtorController@store', $this->debtor), [
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

	public function test_accountant_cant_create_new_invoice() {
		Queue::fake();

		$this->actingAs($this->accountant)->post(action('Admin\DebtorController@store', $this->debtor), [
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
	
	public function test_admin_can_create_new_invoice() {
		
		Queue::fake();
		
		$prefix = app('settings')->get('registration_year');
		$number = Invoice::getNumber();
		
		$this->actingAs($this->user)->post(action('Admin\DebtorInvoiceController@store', $this->debtor), [
			'recipient' => $this->debtor->email,
			'bcc' => $this->debtor->email,
			'message' => 'test',
			'subject' => 'test subject',
			'items' => [[
				'quantity' => 1,
				'unitPrice' => 1,
				'tax' => 6,
				'item' => 'test'
			], [
				'quantity' => 2,
				'unitPrice' => 2,
				'tax' => 21,
				'item' => 'test2'
			]]
		])->assertSuccessful()->assertJson([
			'prefix' => $prefix,
			'number' => $number,
			'tax' => 0,
			'owner_id' => $this->debtor->id,
			'owner_type' => Debtor::class,
			'amount' => 5.9,
			'total' => 5.9,
		]);
		
		$this->assertDatabaseHas('invoices', [
			'prefix' => $prefix,
			'number' => $number,
			'tax' => 0,
			'owner_id' => $this->debtor->id,
			'owner_type' => Debtor::class,
			'amount' => 5.9,
		]);
		
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 1,
			'unit_price' => 1,
			'tax' => 6,
			'name' => 'test'
		]);
		
		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 2,
			'unit_price' => 2,
			'tax' => 21,
			'name' => 'test2'
		]);
		
		Queue::assertPushed(SendDebtorInvoice::class);
	}
	
	public function test_new_invoice_validation() {
		
		Queue::fake();
		
		$this->actingAs($this->user)->post(action('Admin\DebtorInvoiceController@store', $this->debtor), [
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
	}
	
	public function test_new_invoice_business_details_validation() {
		
		Queue::fake();
		
		$this->debtor->data = [];
		$this->debtor->save();
		
		$this->actingAs($this->user)->post(action('Admin\DebtorInvoiceController@store', $this->debtor), [
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['help']);
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
	}
	
	public function test_guest_cant_see_existing_invoice_form() {
		$this->get(action('Admin\DebtorInvoiceController@edit', [
			'debtor' => $this->debtor,
			'invoice' => $this->invoices->first()
		]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_existing_invoice_form() {
		$this->actingAs($this->worker)->get(action('Admin\DebtorInvoiceController@edit', [
			'debtor' => $this->debtor,
			'invoice' => $this->invoices->first()
		]))->assertForbidden();
	}

	public function test_accountant_cant_see_existing_invoice_form() {
		$this->actingAs($this->accountant)->get(action('Admin\DebtorInvoiceController@edit', [
			'debtor' => $this->debtor,
			'invoice' => $this->invoices->first()
		]))->assertForbidden();
	}

	public function test_kitchen_cant_see_existing_invoice_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\DebtorInvoiceController@edit', [
			'debtor' => $this->debtor,
			'invoice' => $this->invoices->first()
		]))->assertForbidden();
	}
	
	public function test_admin_loads_existing_invoice_form() {
		$invoice = $this->invoices->random();
		
		$items = $invoice->items->map(function ($item) {
			return [
				'item' => $item->name,
				'quantity' => $item->quantity,
				'unitPrice' => $item->unit_price,
				'tax' => $item->tax,
			];
		});
		
		$response = $this->actingAs($this->user)->get(action('Admin\DebtorInvoiceController@edit', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]))->assertSuccessful()->assertJsonFragment([
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
	
	public function test_guest_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$this->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
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

	public function test_worker_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();

		$this->actingAs($this->worker)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
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

	public function test_accountant_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();

		$this->actingAs($this->accountant)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
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


	public function test_kitchen_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$this->actingAs($this->kitchen)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
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
	
	
	public function test_admin_can_edit_invoice() {
		
		Queue::fake();
		$invoice = $this->invoices->random();
		
		$prefix = app('settings')->get('registration_year');
		
		$this->actingAs($this->user)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
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
	
	public function test_edit_invoice_validation() {
		
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$this->actingAs($this->user)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
		
	}
	
	public function test_edit_invoice_business_details_validation() {
		
		Queue::fake();
		$invoice = $this->invoices->first();
		$this->debtor->data = [];
		$this->debtor->save();
		
		$this->actingAs($this->user)->patch(action('Admin\DebtorInvoiceController@update', [
			'debtor' => $this->debtor,
			'invoice' => $invoice
		]), [
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['help']);
		
		Queue::assertNotPushed(SendDebtorInvoice::class);
		
	}
	
	
}
