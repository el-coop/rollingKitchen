<?php

namespace Tests\Feature\Admin\Invoices;

use App\Jobs\SendInvoice;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use ConsoleTVs\Invoices\Classes\Invoice as InvoiceFile;
use Exception;
use Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationInvoiceTest extends TestCase {
	
	use RefreshDatabase;
	
	private $user;
	private $kitchen;
	private $application;
	private $invoice;
	private $invoices;
	
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
		
		$this->invoices = factory(Invoice::class, 4)->make();
		$this->invoices->each(function ($invoice) {
			$this->application->invoices()->save($invoice);
			$invoiceItems = rand(1, 4);
			$total = 0;
			for ($j = 0; $j < $invoiceItems; $j++) {
				$invoiceItem = factory(\App\Models\InvoiceItem::class)->make();
				$invoice->items()->save($invoiceItem);
				$total = $invoiceItem->unit_price * $invoiceItem->quantity;
			}
			
			
			$invoice->amount = $total;
			$invoice->save();
			
		});
		
	}
	
	public function test_guest_cant_see_invoice_index_page() {
		$this->get(action('Admin\ApplicationInvoiceController@index'))
			->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_invoice_index_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@index'))
			->assertForbidden();
	}
	
	public function test_admin_can_see_invoice_index_page() {
		$this->actingAs($this->user)->get(action('Admin\ApplicationInvoiceController@index'))
			->assertSuccessful()->assertSee('</datatable>');
	}
	
	public function test_guest_cant_see_new_invoice_form() {
		$this->get(action('Admin\ApplicationInvoiceController@create', $this->application))
			->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_new_invoice_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
			->assertForbidden();
	}
	
	
	public function test_admin_loads_new_invoice_form() {
		$settings = app('settings');
		$language = $this->kitchen->language;
		$this->actingAs($this->user)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
			->assertSuccessful()->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->kitchen->email,
			])
			->assertJsonFragment([[
				'name' => 'subject',
				'label' => __('admin/invoices.subject'),
				'type' => 'text',
				'checked' => true,
				'value' => $settings->get("invoices_default_subject_{$language}", ''),
			]])->assertJsonFragment([
				'name' => 'message',
				'label' => __('admin/invoices.message'),
				'type' => 'textarea',
				'value' => $settings->get("invoices_default_email_{$language}", ''),
			])->assertJsonFragment([
				'name' => 'items',
				'label' => 'Items',
				'type' => 'invoice',
				'value' => [],
				'taxOptions' => [
					'21' => '21%',
					'0' => '0',
				]]);
		
	}
	
	public function test_admin_loads_new_invoice_form_with_outstanding_items() {
		$settings = app('settings');
		$language = $this->kitchen->language;
		$this->invoices->each->delete();
		factory(Service::class, 2)->create()->each(function ($service) {
			$this->application->services()->attach($service, ['quantity' => rand(1, 3)]);
		});
		
		$outstandingItems = [[
			'quantity' => 1,
			'item' => __('admin/invoices.fee', [], $language),
			'unitPrice' => $this->application->data[8]
		], [
			'quantity' => 1,
			'item' => __('kitchen/services.trash', [], $language),
			'unitPrice' => 50
		]];
		foreach ($this->application->services as $service) {
			$outstandingItems[] = [
				'quantity' => intval($service->pivot->quantity),
				'item' => $service->{"name_{$language}"},
				'unitPrice' => $service->price
			];
		}
		
		$this->actingAs($this->user)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
			->assertSuccessful()->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->kitchen->email,
			])
			->assertJsonFragment([[
				'name' => 'subject',
				'label' => __('admin/invoices.subject'),
				'type' => 'text',
				'checked' => true,
				'value' => $settings->get("invoices_default_subject_{$language}", ''),
			]])->assertJsonFragment([
				'name' => 'message',
				'label' => __('admin/invoices.message'),
				'type' => 'textarea',
				'value' => $settings->get("invoices_default_email_{$language}", ''),
			])->assertJsonFragment([
				'name' => 'items',
				'label' => 'Items',
				'type' => 'invoice',
				'value' => $outstandingItems,
				'taxOptions' => [
					'21' => '21%',
					'0' => '0',
				]]);
	}
	
	public function test_guest_cant_create_new_invoice() {
		Queue::fake();
		
		$this->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
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
		
		Queue::assertNotPushed(SendInvoice::class);
	}
	
	public function test_kitchen_cant_create_new_invoice() {
		Queue::fake();
		
		$this->actingAs($this->kitchen)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
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
		
		Queue::assertNotPushed(SendInvoice::class);
	}
	
	
	public function test_admin_can_create_new_invoice() {
		
		Queue::fake();
		
		$prefix = app('settings')->get('registration_year');
		$number = Invoice::getNumber();
		
		$this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
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
			'number' => $number,
			'tax' => 21,
			'application_id' => 1,
			'amount' => 5,
			'total' => 6.05,
			'taxAmount' => 1.05,
		]);
		
		$this->assertDatabaseHas('invoices', [
			'prefix' => $prefix,
			'number' => $number,
			'tax' => 21,
			'application_id' => 1,
			'amount' => 5,
		]);
		
		$this->assertDatabaseHas('applications', [
			'id' => $this->application->id,
			'number' => 1
		]);
		
		Queue::assertPushed(SendInvoice::class);
		
	}
	
	public function test_new_invoice_validation() {
		
		Queue::fake();
		
		
		$this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
			'tax' => '',
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['tax', 'recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendInvoice::class);
		
	}
	
	public function test_guest_cant_see_existing_invoice_form() {
		$this->get(action('Admin\ApplicationInvoiceController@edit', [
			'application' => $this->application,
			'invoice' => $this->invoices->first()
		]))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_existing_invoice_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@edit', [
			'application' => $this->application,
			'invoice' => $this->invoices->first()
		]))->assertForbidden();
	}
	
	public function test_admin_loads_existing_invoice_form() {
		$settings = app('settings');
		$invoice = $this->invoices->first();
		$language = $this->kitchen->language;
		
		$items = $invoice->items->map(function ($item) {
			return [
				'item' => $item->name,
				'quantity' => $item->quantity,
				'unitPrice' => $item->unit_price,
			];
		});
		
		$response = $this->actingAs($this->user)->get(action('Admin\ApplicationInvoiceController@edit', [
			'application' => $this->application,
			'invoice' => $invoice
		]))->assertSuccessful()->assertJsonFragment([
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
	
	public function test_guest_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$this->patch(action('Admin\ApplicationInvoiceController@update', [
			'application' => $this->application,
			'invoice' => $invoice
		]), [
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
		
		Queue::assertNotPushed(SendInvoice::class);
	}
	
	public function test_kitchen_cant_edit_invoice() {
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$this->actingAs($this->kitchen)->patch(action('Admin\ApplicationInvoiceController@update', [
			'application' => $this->application,
			'invoice' => $invoice
		]), [
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
		
		Queue::assertNotPushed(SendInvoice::class);
	}
	
	
	public function test_admin_can_edit_invoice() {
		
		Queue::fake();
		$invoice = $this->invoices->first();
		
		$prefix = app('settings')->get('registration_year');
		
		$this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
			'application' => $this->application,
			'invoice' => $invoice
		]), [
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
			'application_id' => 1,
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
		Queue::assertPushed(SendInvoice::class);
		
	}
	
	public function test_edit_invoice_validation() {
		
		Queue::fake();
		$invoice = $this->invoices->first();
		
		
		$this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
			'application' => $this->application,
			'invoice' => $invoice
		]), [
			'tax' => '',
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['tax', 'recipient', 'bcc', 'message', 'subject', 'items']);
		
		Queue::assertNotPushed(SendInvoice::class);
		
	}
	
	
	public function test_guest_cant_toggle_invoice_status() {
		$invoice = $this->invoices->first();
		
		$this->patch(action('Admin\ApplicationInvoiceController@togglePaid', $invoice))->assertRedirect(action('Auth\LoginController@login'));
		
	}
	
	public function test_kitchen_cant_toggle_invoice_status() {
		
		$invoice = $this->invoices->first();
		
		$this->actingAs($this->kitchen)->patch(action('Admin\ApplicationInvoiceController@togglePaid', $invoice))->assertForbidden();
	}
	
	
	public function test_admin_can_mark_invoice_as_paid() {
		
		$invoice = $this->invoices->first();
		$this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@togglePaid', $invoice))
			->assertSuccessful()
			->assertJson($invoice->toArray());
		
		$this->assertDatabaseHas('invoices', [
			'id' => $invoice->id,
			'paid' => 1
		]);
	}
	
	public function test_admin_can_mark_invoice_as_unpaid() {
		
		$invoice = $this->invoices->first();
		$invoice->paid = true;
		$invoice->save();
		$this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@togglePaid', $invoice))
			->assertSuccessful()
			->assertJsonFragment([
				'id' => $invoice->id,
				'paid' => false
			]);
		
		$this->assertDatabaseHas('invoices', [
			'id' => $invoice->id,
			'paid' => 0
		]);
	}
	
	protected function getSocketData($language) {
		switch ($this->application->socket) {
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
	
	
}
