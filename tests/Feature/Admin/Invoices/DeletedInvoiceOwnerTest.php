<?php

namespace Tests\Feature\Admin\Invoices;

use App\Jobs\SendApplicationInvoice;
use App\Jobs\SendDebtorInvoice;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Debtor;
use App\Models\DeletedInvoiceOwner;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletedInvoiceOwnerTest extends TestCase {
	use RefreshDatabase;

	protected $worker;
	private $user;
	private $kitchen;
	private $accountant;
	private $application;
	private $applicationInvoice;
	private $debtor;
	private $debtorInvoice;
	private $deletedeOwner;
	private $deletedOwnerInvoice;

	protected function setUp(): void {
		parent::setUp();
		$this->user = User::factory()->make();
		Admin::factory()->create()->user()->save($this->user);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create([
			'data' => [
				2 => 'test',
				3 => 'test',
				4 => 'test',
				5 => 'test',
			]
		])->user()->save($this->kitchen);
		$this->application = Application::factory()->make([
			'data' => [
				8 => '1000'
			]
		]);
		$this->kitchen->user->applications()->save($this->application);
		$this->debtor = Debtor::factory()->create();
		$this->applicationInvoice = Invoice::factory()->make();
		$this->application->invoices()->save($this->applicationInvoice);
		$this->applicationInvoice->save();
		$this->debtorInvoice = Invoice::factory()->make();
		$this->debtor->invoices()->save($this->debtorInvoice);
		$this->deletedeOwner = DeletedInvoiceOwner::factory()->create();
		$this->deletedOwnerInvoice = Invoice::factory()->make();
		$this->deletedeOwner->invoices()->save($this->deletedOwnerInvoice);
	}

	public function test_deleted_owner_created_on_kitchen_delete() {
		$this->actingAs($this->user)->delete(action('Admin\KitchenController@destroy', $this->kitchen->user));
		$this->assertDatabaseHas('invoices', [
			'id' => $this->applicationInvoice->id,
			'owner_type' => DeletedInvoiceOwner::class
		]);
		$this->assertDatabaseHas('deleted_invoice_owners', [
			'email' => $this->kitchen->email,
			'name' => $this->kitchen->name,
		]);
		$deletedInvoiceOwner = DeletedInvoiceOwner::where('name', $this->kitchen->name)->first();
		$this->assertEquals($this->kitchen->user->data->toArray(), $deletedInvoiceOwner->data);
	}

	public function test_deleted_owner_created_on_debtor_delete() {
		$this->actingAs($this->user)->delete(action('Admin\DebtorController@destroy', $this->debtor));
		$this->assertDatabaseHas('invoices', [
			'id' => $this->debtorInvoice->id,
			'owner_type' => DeletedInvoiceOwner::class
		]);
		$this->assertDatabaseHas('deleted_invoice_owners', [
			'email' => $this->debtor->email,
			'name' => $this->debtor->name,
		]);
        $deletedInvoiceOwner = DeletedInvoiceOwner::where('name', $this->debtor->name)->first();
        $this->assertEquals($this->debtor->data->toArray(), $deletedInvoiceOwner->data);
	}

	public function test_guest_cant_get_deleted_invoice_owner() {
		$this->get(action('Admin\DeletedInvoiceOwnerController@edit',
			[$this->deletedeOwner,
				$this->deletedOwnerInvoice]))
			->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_get_deleted_invoice_owner() {
		$this->actingAs($this->worker)->get(action('Admin\DeletedInvoiceOwnerController@edit',
			[$this->deletedeOwner,
				$this->deletedOwnerInvoice]))
			->assertForbidden();
	}

	public function test_accountant_cant_get_deleted_invoice_owner() {
		$this->actingAs($this->accountant)->get(action('Admin\DeletedInvoiceOwnerController@edit',
			[$this->deletedeOwner,
				$this->deletedOwnerInvoice]))
			->assertForbidden();
	}

	public function test_kitchen_cant_get_deleted_invoice_owner() {
		$this->actingAs($this->kitchen)->get(action('Admin\DeletedInvoiceOwnerController@edit',
			[$this->deletedeOwner,
				$this->deletedOwnerInvoice]))
			->assertForbidden();
	}

	public function test_admin_can_get_deleted_invoice_owner() {
		$this->actingAs($this->user)->get(action('Admin\DeletedInvoiceOwnerController@edit', [
				$this->deletedeOwner,
				$this->deletedOwnerInvoice])
		)
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'recipient',
				'label' => __('admin/invoices.recipient'),
				'type' => 'text',
				'value' => $this->deletedeOwner->email,
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
			]);
	}

	public function test_guest_cant_update_deleted_owner_invoice() {
		Queue::fake();
		$this->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => 21,
			'recipient' => $this->deletedeOwner->email,
			'bcc' => $this->deletedeOwner->email,
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

	public function test_worker_cant_update_deleted_owner_invoice() {
		Queue::fake();
		$this->actingAs($this->worker)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => 21,
			'recipient' => $this->deletedeOwner->email,
			'bcc' => $this->deletedeOwner->email,
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

	public function test_accountant_cant_update_deleted_owner_invoice() {
		Queue::fake();
		$this->actingAs($this->accountant)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => 21,
			'recipient' => $this->deletedeOwner->email,
			'bcc' => $this->deletedeOwner->email,
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

	public function test_kitchen_cant_update_deleted_owner_invoice() {
		Queue::fake();
		$this->actingAs($this->kitchen)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => 21,
			'recipient' => $this->deletedeOwner->email,
			'bcc' => $this->deletedeOwner->email,
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

	public function test_admin_can_update_deleted_owner_invoice() {
		Queue::fake();

		$prefix = app('settings')->get('registration_year');

		$this->actingAs($this->user)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'recipient' => $this->deletedeOwner->email,
			'bcc' => $this->deletedeOwner->email,
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
			'number' => $this->deletedOwnerInvoice->number,
			'tax' => 0,
			'owner_id' => $this->deletedeOwner->id,
			'owner_type' => DeletedInvoiceOwner::class,
			'amount' => 5.84,
			'total' => 5.84,
			'taxAmount' => 0,
		]);

		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 1,
			'unit_price' => 1,
			'tax' => 0,
			'name' => 'test',
			'invoice_id' => $this->deletedOwnerInvoice->id
		]);

		$this->assertDatabaseHas('invoice_items', [
			'quantity' => 2,
			'unit_price' => 2,
			'tax' => 21,
			'name' => 'test2',
			'invoice_id' => $this->deletedOwnerInvoice->id
		]);

		$this->assertDatabaseHas('invoices', [
			'id' => $this->deletedOwnerInvoice->id,
			'number' => $this->deletedOwnerInvoice->number,
			'tax' => 0,
			'amount' => 5.84,
		]);
		$this->assertCount(2, $this->deletedOwnerInvoice->items);
		Queue::assertPushed(SendDebtorInvoice::class);
	}

	public function test_update_invoice_business_details_validation() {

		Queue::fake();

		$this->deletedeOwner->data = [];
		$this->deletedeOwner->save();


		$this->actingAs($this->user)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => '',
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['help']);

		Queue::assertNotPushed(SendDebtorInvoice::class);

	}

	public function test_edit_invoice_validation() {

		Queue::fake();

		$this->actingAs($this->user)->patch(action('Admin\DeletedInvoiceOwnerController@update', [
			$this->deletedeOwner,
			$this->deletedOwnerInvoice
		]), [
			'tax' => '',
			'recipient' => 'test',
			'bcc' => 'test',
			'message' => '',
			'subject' => '',
			'items' => 'test'
		])->assertRedirect()->assertSessionHasErrors(['recipient', 'bcc', 'message', 'subject', 'items']);

		Queue::assertNotPushed(SendDebtorInvoice::class);
	}
}
