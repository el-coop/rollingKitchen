<?php

namespace Tests\Feature\Kitchens;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\ElectricDevice;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use ElCoop\Valuestore\Valuestore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UsePastApplicationTest extends TestCase {
    use RefreshDatabase;

    protected $worker;
    private $admin;
    private $application;
    private $pastApplication;
    protected $accountant;
    private $kitchen;
    private $product;
    private $kitchen2;
    private $device;
    private $service;

    protected function setUp(): void {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('test.valuestore.json', '');
        $path = Storage::path('test.valuestore.json');
        $this->app->singleton('settings', function ($app) use ($path) {
            return new Valuestore($path);
        });
        $settings = app('settings');
        $settings->put('general_registration_status', true);
        $settings->put('registration_year', 2020);

        $this->admin = User::factory()->make();
        Admin::factory()->create()->user()->save($this->admin);

        $this->worker = User::factory()->make();
        Worker::factory()->create()->user()->save($this->worker);

        $this->kitchen2 = User::factory()->make();
        $this->kitchen2->user()->save(Kitchen::factory()->create());

        $this->accountant = User::factory()->make();
        Accountant::factory()->create()->user()->save($this->accountant);

        $this->kitchen = User::factory()->make();
        $kitchen = Kitchen::factory()->create();
        $kitchen->user()->save($this->kitchen);

        $this->pastApplication = Application::factory()->make(['year' => 2018]);
        $kitchen->applications()->save($this->pastApplication);

        $this->product = Product::factory()->make();
        $this->pastApplication->products()->save($this->product);
        $this->device = ElectricDevice::factory()->make();

        $this->pastApplication->electricDevices()->save($this->device);
        $this->service = Service::factory()->create();
        $this->pastApplication->services()->attach($this->service, ['quantity' => 2]);
        $this->application = Application::factory()->make(['year' => $settings->get('registration_year'), 'status' => 'new']);
        $this->kitchen->user->applications()->save($this->application);
    }

    public function test_guest_cant_use_past_application() {
        $this->patch(action('Kitchen\KitchenController@usePastApplication', $this->application), [
            'pastApplication' => $this->pastApplication->id
        ])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_can_use_past_application() {
        $this->actingAs($this->kitchen)->patch(action('Kitchen\KitchenController@usePastApplication', $this->application), [
            'pastApplication' => $this->pastApplication->id
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $this->kitchen->lnaguage)
        ]);
        $this->assertDatabaseHas('applications', [
            'length' => $this->pastApplication->length,
            'width' => $this->pastApplication->width,
            'terrace_length' => $this->pastApplication->terrace_length,
            'terrace_width' => $this->pastApplication->terrace_width,

        ]);
        $this->assertDatabaseHas('products', [
            'name' => $this->product->name,
            'price' => $this->product->price,
            'category' => $this->product->category,
            'application_id' => $this->application->id
        ]);
        $this->assertDatabaseHas('electric_devices', [
            'name' => $this->device->name,
            'watts' => $this->device->watts,
            'application_id' => $this->application->id
        ]);
        $service = $this->pastApplication->services->first();
        $this->assertDatabaseHas('application_service', [
            'service_id' => $this->service->id,
            'application_id' => $this->application->id,
            'quantity' => $service->pivot->quantity
        ]);
    }

    public function test_other_kitchen_cant_use_past_application() {
        $this->actingAs($this->kitchen2)->patch(action('Kitchen\KitchenController@usePastApplication', $this->application), [
            'pastApplication' => $this->pastApplication->id
        ])->assertForbidden();
    }

    public function test_admin_can_use_past_application() {
        $this->actingAs($this->admin)->patch(action('Kitchen\KitchenController@usePastApplication', $this->application), [
            'pastApplication' => $this->pastApplication->id
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $this->kitchen->lnaguage)
        ]);
        $this->assertDatabaseHas('applications', [
            'length' => $this->pastApplication->length,
            'width' => $this->pastApplication->width,
            'terrace_length' => $this->pastApplication->terrace_length,
            'terrace_width' => $this->pastApplication->terrace_width,

        ]);
        $this->assertDatabaseHas('products', [
            'name' => $this->product->name,
            'price' => $this->product->price,
            'category' => $this->product->category,
            'application_id' => $this->application->id
        ]);
        $this->assertDatabaseHas('electric_devices', [
            'name' => $this->device->name,
            'watts' => $this->device->watts,
            'application_id' => $this->application->id
        ]);
        $service = $this->pastApplication->services->first();
        $this->assertDatabaseHas('application_service', [
            'service_id' => $this->service->id,
            'application_id' => $this->application->id,
            'quantity' => $service->pivot->quantity
        ]);
    }

}
