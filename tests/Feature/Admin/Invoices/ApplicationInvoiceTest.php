<?php

namespace Tests\Feature\Admin\Invoices;

use App\Jobs\SendApplicationInvoice;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Developer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use ConsoleTVs\Invoices\Classes\Invoice as InvoiceFile;
use Throwable;
use Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationInvoiceTest extends TestCase {

    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $kitchen;
    private $application;
    private $accountant;
    private $invoice;
    private $invoices;
    private $payment;
    private $worker;
    private $draftInvoice;
    private $developer;

    public function setUp(): void {
        parent::setUp();
        $this->developer = User::factory()->make();
        Developer::factory()->create()->user()->save($this->developer);
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
            ],
        ])->user()->save($this->kitchen);
        $this->application = Application::factory()->make([
            'data' => [
                8 => '1000',
            ],
        ]);
        $this->kitchen->user->applications()->save($this->application);

        $this->invoices = Invoice::factory(4)->make();
        $this->invoices->each(function ($invoice) {
            $this->application->invoices()->save($invoice);
            $invoiceItems = rand(1, 4);
            $total = 0;
            for ($j = 0; $j < $invoiceItems; $j++) {
                $invoiceItem = InvoiceItem::factory()->make();
                $invoice->items()->save($invoiceItem);
                $total = $invoiceItem->unit_price * $invoiceItem->quantity;
            }

            $invoice->amount = $total;
            $invoice->save();
        });
        $this->draftInvoice = Invoice::factory()->make(['number' => 0]);
        $this->application->invoices()->save($this->draftInvoice);
        $invoiceItems = rand(1, 4);
        $total = 0;
        for ($j = 0; $j < $invoiceItems; $j++) {
            $invoiceItem = InvoiceItem::factory()->make();
            $this->draftInvoice->items()->save($invoiceItem);
            $total = $invoiceItem->unit_price * $invoiceItem->quantity;
        }

        $this->draftInvoice->amount = $total;
        $this->draftInvoice->save();

        $this->payment = InvoicePayment::factory()->make();
        $this->invoices->first()->payments()->save($this->payment);

    }

    public function test_guest_cant_see_invoice_index_page() {
        $this->get(action('Admin\ApplicationInvoiceController@index'))
            ->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_worker_cant_see_invoice_index_page() {
        $this->actingAs($this->worker)->get(action('Admin\ApplicationInvoiceController@index'))
            ->assertForbidden();
    }

    public function test_kitchen_cant_see_invoice_index_page() {
        $this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@index'))
            ->assertForbidden();
    }

    public function test_accountant_cant_see_invoice_index_page() {
        $this->actingAs($this->accountant)->get(action('Admin\ApplicationInvoiceController@index'))
            ->assertForbidden();
    }

    public function test_admin_can_see_invoice_index_page() {
        $this->actingAs($this->user)->get(action('Admin\ApplicationInvoiceController@index'))
            ->assertSuccessful()->assertSee('</datatable>', false);
    }

    public function test_guest_cant_see_new_invoice_form() {
        $this->get(action('Admin\ApplicationInvoiceController@create', $this->application))
            ->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_worker_cant_see_new_invoice_form() {
        $this->actingAs($this->worker)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
            ->assertForbidden();
    }

    public function test_kitchen_cant_see_new_invoice_form() {
        $this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
            ->assertForbidden();
    }

    public function test_accountant_cant_see_new_invoice_form() {
        $this->actingAs($this->accountant)->get(action('Admin\ApplicationInvoiceController@create', $this->application))
            ->assertForbidden();
    }

    public function test_admin_loads_new_invoice_form() {
        $defaultPdf = Pdf::factory()->create([
            'name' => 'test',
            'visibility' => 0,
            'file' => 'test',
            'default_send_invoice' => true,
        ]);
        $pdf = Pdf::factory()->create([
            'name' => 'test2',
            'visibility' => 0,
            'file' => 'test2',
        ]);
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
                ]])->assertJsonFragment([
                'name' => 'attachments',
                'label' => __('admin/invoices.attachments'),
                'type' => 'checkbox',
                'options' => [
                    $defaultPdf->id => [
                        'name' => $defaultPdf->name,
                        'checked' => true,
                    ],
                    $pdf->id => [
                        'name' => $pdf->name,
                        'checked' => false,
                    ],

                ],
            ]);

    }

    public function test_admin_loads_new_invoice_form_with_outstanding_items() {
        $settings = app('settings');
        $language = $this->kitchen->language;
        $this->invoices->each->delete();
        Service::factory(2)->create()->each(function ($service) {
            $this->application->services()->attach($service, ['quantity' => rand(1, 3)]);
        });

        foreach ($this->application->services as $service) {
            $outstandingItems[] = [
                'quantity' => intval($service->pivot->quantity),
                'item' => $service->{"name_{$language}"},
                'unitPrice' => $service->price,
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
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertRedirect(action('Auth\LoginController@login'));

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }

    public function test_worker_cant_create_new_invoice() {
        Queue::fake();

        $this->actingAs($this->worker)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
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
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }

    public function test_accountant_cant_create_new_invoice() {
        Queue::fake();

        $this->actingAs($this->accountant)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
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
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
            'note' => 'test',
            'send' => true,
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
            'total' => 11.05,
            'taxAmount' => 1.05,
            'note' => 'test',
            'extra_amount' => 5,
            'extra_name' => 'test'
        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'number' => 1,
        ]);

        Queue::assertPushed(SendApplicationInvoice::class);

    }

    public function test_updates_services_count_with_new_invoice() {

        Queue::fake();
        $services = Service::factory(2)->create()->each(function ($service) {
            $this->application->services()->attach($service, ['quantity' => 2]);
        });

        $service = $services->first();

        $prefix = app('settings')->get('registration_year');
        $number = Invoice::getNumber();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 3,
                'unitPrice' => 1,
                'item' => $service->name_en,
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
            'note' => 'test',
            'send' => true,
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 7,
            'total' => 13.47,
            'taxAmount' => 1.47,
            'extra_amount' => 5,
            'extra_name' => 'test',
            'note' => 'test'
        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 7,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'number' => 1,
        ]);

        $this->assertEquals(3, $this->application->serviceQuantity($service));
    }

    public function test_new_service_relationship_new_invoice() {

        Queue::fake();
        $service = Service::factory()->create();

        $prefix = app('settings')->get('registration_year');
        $number = Invoice::getNumber();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 2,
                'unitPrice' => 1,
                'item' => $service->name_en,
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
            'note' => 'test',
            'send' => true,
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 6,
            'extra_amount' => 5,
            'extra_name' => 'test',
            'note' => 'test'
        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 6,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'number' => 1,
        ]);

        $this->assertEquals(2, $this->application->serviceQuantity($service));
    }

    public function test_new_invoice_validation() {

        Queue::fake();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => '',
            'recipient' => 'test',
            'bcc' => 'test',
            'message' => '',
            'subject' => '',
            'items' => 'test',
            'send' => true
        ])->assertRedirect()->assertSessionHasErrors(['tax', 'recipient', 'bcc', 'message', 'subject', 'items']);

        Queue::assertNotPushed(SendApplicationInvoice::class);

    }

    public function test_new_invoice_business_details_validation() {

        Queue::fake();

        $this->kitchen->user->data = json_encode([]);;
        $this->kitchen->user->save();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => '',
            'recipient' => 'test',
            'bcc' => 'test',
            'message' => '',
            'subject' => '',
            'items' => 'test',
        ])->assertRedirect()->assertSessionHasErrors(['help']);

        Queue::assertNotPushed(SendApplicationInvoice::class);

    }

    public function test_guest_cant_see_existing_invoice_form() {
        $this->get(action('Admin\ApplicationInvoiceController@edit', [
            'application' => $this->application,
            'invoice' => $this->invoices->first(),
        ]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_worker_cant_see_existing_invoice_form() {
        $this->actingAs($this->worker)->get(action('Admin\ApplicationInvoiceController@edit', [
            'application' => $this->application,
            'invoice' => $this->invoices->first(),
        ]))->assertForbidden();
    }


    public function test_kitchen_cant_see_existing_invoice_form() {
        $this->actingAs($this->kitchen)->get(action('Admin\ApplicationInvoiceController@edit', [
            'application' => $this->application,
            'invoice' => $this->invoices->first(),
        ]))->assertForbidden();
    }

    public function test_accountant_cant_see_existing_invoice_form() {
        $this->actingAs($this->accountant)->get(action('Admin\ApplicationInvoiceController@edit', [
            'application' => $this->application,
            'invoice' => $this->invoices->first(),
        ]))->assertForbidden();
    }

    public function test_admin_loads_existing_invoice_form() {
        $defaultPdf = Pdf::factory()->create([
            'name' => 'test',
            'visibility' => 0,
            'file' => 'test',
            'default_resend_invoice' => true,
        ]);
        $pdf = Pdf::factory()->create([
            'name' => 'test2',
            'visibility' => 0,
            'file' => 'test2',
        ]);
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
            'invoice' => $invoice,
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
            ]])->assertJsonFragment([
            'name' => 'attachments',
            'label' => __('admin/invoices.attachments'),
            'type' => 'checkbox',
            'options' => [
                $defaultPdf->id => [
                    'name' => $defaultPdf->name,
                    'checked' => true,
                ],
                $pdf->id => [
                    'name' => $pdf->name,
                    'checked' => false,
                ],

            ],
        ]);

        foreach ($items as $item) {
            $response->assertJsonFragment($item);
        }
    }

    public function test_guest_cant_edit_invoice() {
        Queue::fake();
        $invoice = $this->invoices->first();

        $this->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertRedirect(action('Auth\LoginController@login'));

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }

    public function test_worker_cant_edit_invoice() {
        Queue::fake();
        $invoice = $this->invoices->first();

        $this->actingAs($this->worker)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }


    public function test_kitchen_cant_edit_invoice() {
        Queue::fake();
        $invoice = $this->invoices->first();

        $this->actingAs($this->kitchen)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }

    public function test_accountant_cant_edit_invoice() {
        Queue::fake();
        $invoice = $this->invoices->first();

        $this->actingAs($this->kitchen)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertForbidden();

        Queue::assertNotPushed(SendApplicationInvoice::class);
    }

    public function test_admin_can_edit_invoice() {

        Queue::fake();
        $invoice = $this->invoices->first();

        $prefix = app('settings')->get('registration_year');

        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $invoice->number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
            'total' => 6.05,
            'taxAmount' => 1.05,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 1,
            'unit_price' => 1,
            'name' => 'test',
            'invoice_id' => $invoice->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 2,
            'unit_price' => 2,
            'name' => 'test2',
            'invoice_id' => $invoice->id,
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

    public function test_updates_services_count_with_edited_invoice() {

        Queue::fake();
        $services = Service::factory(2)->create()->each(function ($service) {
            $this->application->services()->attach($service, ['quantity' => 1]);
        });

        $service = $services->first();

        $invoice = $this->invoices->first();

        $prefix = app('settings')->get('registration_year');

        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 2,
                'unitPrice' => 1,
                'item' => $service->name_nl,
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $invoice->number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 6,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 2,
            'unit_price' => 1,
            'name' => $service->name_nl,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 2,
            'unit_price' => 2,
            'name' => 'test2',
            'invoice_id' => $invoice->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'number' => $invoice->number,
            'tax' => 21,
            'amount' => 6,
        ]);
        $this->assertCount(2, $invoice->items);

        $this->assertEquals(2, $this->application->serviceQuantity($service));
    }

    public function test_new_service_relationship_on_updated_invoice() {

        Queue::fake();
        $service = Service::factory()->create();

        $invoice = $this->invoices->first();

        $prefix = app('settings')->get('registration_year');

        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 3,
                'unitPrice' => 1,
                'item' => $service->name_nl,
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $invoice->number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 7,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 3,
            'unit_price' => 1,
            'name' => $service->name_nl,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 2,
            'unit_price' => 2,
            'name' => 'test2',
            'invoice_id' => $invoice->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'number' => $invoice->number,
            'tax' => 21,
            'amount' => 7,
        ]);
        $this->assertCount(2, $invoice->items);

        $this->assertEquals(3, $this->application->serviceQuantity($service));
    }

    public function test_edit_invoice_business_details_validation() {

        Queue::fake();
        $invoice = $this->invoices->first();

        $this->kitchen->user->data = json_encode([]);;
        $this->kitchen->user->save();


        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => '',
            'recipient' => 'test',
            'bcc' => 'test',
            'message' => '',
            'subject' => '',
            'items' => 'test',
        ])->assertRedirect()->assertSessionHasErrors(['help']);

        Queue::assertNotPushed(SendApplicationInvoice::class);

    }

    public function test_edit_invoice_validation() {

        Queue::fake();
        $invoice = $this->invoices->first();

        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $invoice,
        ]), [
            'tax' => '',
            'recipient' => 'test',
            'bcc' => 'test',
            'message' => '',
            'subject' => '',
            'items' => 'test',
            'send' => true
        ])->assertRedirect()->assertSessionHasErrors(['tax', 'recipient', 'bcc', 'message', 'subject', 'items']);

        Queue::assertNotPushed(SendApplicationInvoice::class);

    }

    public function test_guest_cant_add_payment() {
        $invoice = $this->invoices->first();
        $this->post(action('Admin\ApplicationInvoiceController@addPayment', $invoice))->assertRedirect(action('Auth\LoginController@login'));

    }

    public function test_worker_cant_add_payment() {

        $invoice = $this->invoices->first();

        $this->actingAs($this->worker)->post(action('Admin\ApplicationInvoiceController@addPayment', $invoice))->assertForbidden();
    }

    public function test_kitchen_cant_add_payment() {

        $invoice = $this->invoices->first();

        $this->actingAs($this->kitchen)->post(action('Admin\ApplicationInvoiceController@addPayment', $invoice))->assertForbidden();
    }

    public function test_accountant_cant_add_payment() {

        $invoice = $this->invoices->first();

        $this->actingAs($this->accountant)->post(action('Admin\ApplicationInvoiceController@addPayment', $invoice))->assertForbidden();
    }

    public function test_admin_can_add_payment() {
        $date = $this->faker()->date();
        $invoice = $this->invoices->first();
        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@addPayment', $invoice), [
            'amount' => 100,
            'date' => $date,
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('invoice_payments', [
            'invoice_id' => $invoice->id,
            'amount' => 100,
            'date' => $date,
        ]);
        $this->assertEquals($invoice->totalPaid, 100 + $this->payment->amount);
    }

    public function test_guest_cant_update_payment() {
        $invoice = $this->invoices->first();
        $this->patch(action('Admin\ApplicationInvoiceController@updatePayment', [$invoice, $this->payment]))->assertRedirect(action('Auth\LoginController@login'));

    }

    public function test_worker_cant_update_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->worker)->patch(action('Admin\ApplicationInvoiceController@updatePayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_kitchen_cant_update_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->kitchen)->patch(action('Admin\ApplicationInvoiceController@updatePayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_accountant_cant_update_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->accountant)->patch(action('Admin\ApplicationInvoiceController@updatePayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_admin_can_update_payment() {
        $invoice = $this->invoices->first();
        $date = $this->faker()->date();
        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@updatePayment', [$invoice, $this->payment]), [
            'amount' => 500,
            'date' => $date,
        ])->assertSuccessful();
        $this->assertDatabaseHas('invoice_payments', [
            'id' => $this->payment->id,
            'date' => $date,
            'amount' => 500,
        ]);
    }

    public function test_guest_cant_destroy_payment() {
        $invoice = $this->invoices->first();
        $this->delete(action('Admin\ApplicationInvoiceController@destroyPayment', [$invoice, $this->payment]))->assertRedirect(action('Auth\LoginController@login'));

    }

    public function test_worker_cant_destroy_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->worker)->delete(action('Admin\ApplicationInvoiceController@destroyPayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_kitchen_cant_destroy_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->kitchen)->delete(action('Admin\ApplicationInvoiceController@destroyPayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_accountant_cant_destroy_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->accountant)->delete(action('Admin\ApplicationInvoiceController@destroyPayment', [$invoice, $this->payment]))->assertForbidden();
    }

    public function test_admin_can_destroy_payment() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->user)->delete(action('Admin\ApplicationInvoiceController@destroyPayment', [$invoice, $this->payment]))->assertSuccessful();
        $this->assertDatabaseMissing('invoice_payments', [
            'id' => $this->payment->id,
        ]);
    }


    public function test_service_relationship_removed_when_invoice_brings_it_to_0() {

        Queue::fake();
        $services = Service::factory(2)->create()->each(function ($service) {
            $this->application->services()->attach($service, ['quantity' => 2]);
        });
        $service = $services->first();

        $firstInvoice = Invoice::factory()->create([
            'owner_id' => $this->application->id,
            'owner_type' => Application::class
        ]);

        $firstInvoice->items()->save(InvoiceItem::factory()->make([
            'service_id' => $service->id,
            'quantity' => 2
        ]));


        $prefix = app('settings')->get('registration_year');
        $number = Invoice::getNumber();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => -2,
                'unitPrice' => 1,
                'item' => $service->name_en,
            ]],
            'note' => 'test',
            'send' => true,
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => -2,
            'total' => 2.58,
            'taxAmount' => -0.42,
            'extra_amount' => 5,
            'extra_name' => 'test',
            'note' => 'test'
        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => -2,
        ]);

        $this->assertDatabaseMissing('application_service', [
            'application_id' => $this->application->id,
            'service_id' => $service->id,
        ]);

        $this->assertEquals(0, $this->application->serviceQuantity($service));
    }

    public function test_service_relationship_reduced_when_invoice_brings_it_to_smaller_amount() {

        Queue::fake();
        $services = Service::factory(2)->create()->each(function ($service) {
            $this->application->services()->attach($service, ['quantity' => 2]);
        });
        $service = $services->first();

        $firstInvoice = Invoice::factory()->create([
            'owner_id' => $this->application->id,
            'owner_type' => Application::class
        ]);

        $firstInvoice->items()->save(InvoiceItem::factory()->make([
            'service_id' => $service->id,
            'quantity' => 2
        ]));


        $prefix = app('settings')->get('registration_year');
        $number = Invoice::getNumber();

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => -1,
                'unitPrice' => 1,
                'item' => $service->name_en,
            ]],
            'note' => 'test',
            'send' => true,
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => -1,
            'total' => 3.79,
            'taxAmount' => -0.21,
            'extra_amount' => 5,
            'extra_name' => 'test',
            'note' => 'test'

        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => $number,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => -1,
        ]);

        $this->assertDatabaseHas('application_service', [
            'application_id' => $this->application->id,
            'service_id' => $service->id,
            'quantity' => 1
        ]);

        $this->assertEquals(1, $this->application->serviceQuantity($service));
    }

    public function test_admin_can_save_invoice_as_draft() {
        Queue::fake();

        $prefix = app('settings')->get('registration_year');

        $this->actingAs($this->user)->post(action('Admin\ApplicationInvoiceController@store', $this->application), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
            'note' => 'test',
            'extra_amount' => 5,
            'extra_name' => 'test'
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => 0,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
            'total' => 11.05,
            'taxAmount' => 1.05,
            'note' => 'test',
            'extra_amount' => 5,
            'extra_name' => 'test'
        ]);

        $this->assertDatabaseHas('invoices', [
            'prefix' => $prefix,
            'number' => 0,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'number' => 1,
        ]);

        Queue::assertNothingPushed();
    }

    public function test_admin_can_edit_draft_invoice() {

        Queue::fake();

        $prefix = app('settings')->get('registration_year');

        $this->actingAs($this->user)->patch(action('Admin\ApplicationInvoiceController@update', [
            'application' => $this->application,
            'invoice' => $this->draftInvoice,
        ]), [
            'tax' => 21,
            'recipient' => $this->kitchen->email,
            'bcc' => $this->user->email,
            'message' => 'test',
            'subject' => 'test subject',
            'items' => [[
                'quantity' => 1,
                'unitPrice' => 1,
                'item' => 'test',
            ], [
                'quantity' => 2,
                'unitPrice' => 2,
                'item' => 'test2',
            ]],
        ])->assertSuccessful()->assertJson([
            'prefix' => $prefix,
            'number' => 0,
            'tax' => 21,
            'owner_id' => $this->application->id,
            'owner_type' => Application::class,
            'amount' => 5,
            'total' => 6.05,
            'taxAmount' => 1.05,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 1,
            'unit_price' => 1,
            'name' => 'test',
            'invoice_id' => $this->draftInvoice->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'quantity' => 2,
            'unit_price' => 2,
            'name' => 'test2',
            'invoice_id' => $this->draftInvoice->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->draftInvoice->id,
            'number' => 0,
            'tax' => 21,
            'amount' => 5,
        ]);
        $this->assertCount(2, $this->draftInvoice->items);
        Queue::assertNothingPushed();
    }

    public function test_guest_cant_delete_invoice() {
        $this->delete(action('Admin\ApplicationInvoiceController@destroy', $this->invoices->first()))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_delete_invoice() {
        $this->actingAs($this->kitchen)->delete(action('Admin\ApplicationInvoiceController@destroy', $this->invoices->first()))->assertForbidden();
    }

    public function test_accountant_cant_delete_invoice() {
        $this->actingAs($this->accountant)->delete(action('Admin\ApplicationInvoiceController@destroy', $this->invoices->first()))->assertForbidden();
    }

    public function test_worker_cant_delete_invoice() {
        $this->actingAs($this->worker)->delete(action('Admin\ApplicationInvoiceController@destroy', $this->invoices->first()))->assertForbidden();
    }

    public function test_admin_can_delete_invoice() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->user)->delete(action('Admin\ApplicationInvoiceController@destroy', $invoice))->assertSuccessful();
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

    }

    public function test_developer_can_delete_invoice() {
        $invoice = $this->invoices->first();
        $this->actingAs($this->developer)->delete(action('Admin\ApplicationInvoiceController@destroy', $invoice))->assertSuccessful();
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

    }


}
