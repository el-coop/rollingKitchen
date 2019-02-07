<?php

namespace Tests\Feature\Kitchens;

use App\Models\Admin;
use App\Models\Application;
use App\Models\ElectricDevice;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use ElCoop\Valuestore\Valuestore;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationDeviceTest extends TestCase {
	use RefreshDatabase;

	protected $worker;
	private $admin;
	private $application;
	private $kitchen;
	private $kitchen2;
	private $device;

	protected function setUp() {
		parent::setUp();

		Storage::fake('local');
		Storage::disk('local')->put('test.valuestore.json', '');
		$path = Storage::path('test.valuestore.json');
		$this->app->singleton('settings', function ($app) use ($path) {
			return new Valuestore($path);
		});
		$settings = app('settings');
		$settings->put('general_registration_status', true);
		$settings->put('registration_year', 2018);


		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);

		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);

		$this->kitchen2 = factory(User::class)->make();
		$this->kitchen2->user()->save(factory(Kitchen::class)->create());

		$this->kitchen = factory(User::class)->make();
		$kitchen = factory(Kitchen::class)->create();
		$kitchen->user()->save($this->kitchen);

		$this->application = factory(Application::class)->make(['year' => $settings->get('registration_year')]);
		$kitchen->applications()->save($this->application);

		$this->device = factory(ElectricDevice::class)->make();

		$this->application->electricDevices()->save($this->device);

		$path = Storage::path('test.valuestore');
	}

	public function test_a_guest_cant_create_device() {
		$this->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3
		])->assertRedirect(action('Auth\LoginController@login'));
	}
	public function test_a_different_worker_cant_create_device() {
		$this->actingAs($this->worker)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3
		])->assertForbidden();
	}
	public function test_a_different_kitchen_cant_create_device() {
		$this->actingAs($this->kitchen2)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3
		])->assertForbidden();
	}

	public function test_admin_can_create_device() {
		$this->actingAs($this->admin)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3,
		])->assertSuccessful();

		$this->assertDatabaseHas('electric_devices', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'watts' => 3,
		]);
	}


	public function test_kitchen_can_create_device_in_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3,
		])->assertSuccessful();

		$this->assertDatabaseHas('electric_devices', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'watts' => 3,
		]);
	}

	public function test_kitchen_cant_create_device_in_submitted_application() {

		$this->actingAs($this->kitchen)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => 'test',
			'watts' => 3,
		])->assertForbidden();
	}

	public function test_device_create_validation() {
		$this->actingAs($this->admin)->post(action('Kitchen\ApplicationDeviceController@create', $this->application), [
			'name' => '',
			'watts' => 'blaa',
		])->assertSessionHasErrors(['name', 'watts']);
	}

	public function test_a_guest_cant_edit_device() {
		$this->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => 'test',
			'watts' => 0.01
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_a_different_kitchen_cant_edit_device() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen2)->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => 'test',
			'watts' => 0.01
		])->assertForbidden();
	}


	public function test_admin_can_edit_device() {
		$this->actingAs($this->admin)->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => 'test',
			'watts' => 0.01
		])->assertSuccessful();

		$this->assertDatabaseHas('electric_devices', [
			'id' => $this->device->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'watts' => 0.01
		]);
	}

	public function test_kitchen_can_edit_device_on_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => 'test',
			'watts' => 0.01
		])->assertSuccessful();

		$this->assertDatabaseHas('electric_devices', [
			'id' => $this->device->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'watts' => 0.01
		]);
	}

	public function test_kitchen_cant_edit_device_on_open_application() {
		$this->actingAs($this->kitchen)->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => 'test',
			'watts' => 0.01
		])->assertForbidden();

		$this->assertDatabaseMissing('electric_devices', [
			'id' => $this->device->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'watts' => 0.01
		]);
	}


	public function test_validation_device_edit() {
		$this->actingAs($this->admin)->patch(action('Kitchen\ApplicationDeviceController@update', ['application' => $this->application, 'device' => $this->device]), [
			'name' => '',
			'watts' => 'gla'
		])->assertSessionHasErrors(['name', 'watts']);

		$this->assertDatabaseMissing('electric_devices', [
			'id' => $this->device->id,
			'application_id' => $this->application->id,
			'name' => '',
			'price' => 'gla'
		]);
	}

	public function test_a_guest_cant_delete_device() {
		$this->delete(action('Kitchen\ApplicationDeviceController@destroy', ['application' => $this->application, 'device' => $this->device]))
			->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_a_different_kitchen_cant_delete_device() {
		$this->actingAs($this->kitchen2)->delete(action('Kitchen\ApplicationDeviceController@destroy', ['application' => $this->application, 'device' => $this->device]))
			->assertForbidden();
	}

	public function test_admin_can_delete_device() {
		$this->actingAs($this->admin)->delete(action('Kitchen\ApplicationDeviceController@destroy', ['application' => $this->application, 'device' => $this->device]))->assertSuccessful();

		$this->assertDatabaseMissing('electric_devices', [
			'id' => $this->device->id,
		]);
	}

	public function test_kitchen_can_delete_device_on_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->delete(action('Kitchen\ApplicationDeviceController@destroy', ['application' => $this->application, 'device' => $this->device]))->assertSuccessful();

		$this->assertDatabaseMissing('electric_devices', [
			'id' => $this->device->id,
		]);
	}


	public function test_kitchen_cant_delete_device_on_closed_application() {
		$this->actingAs($this->kitchen)->delete(action('Kitchen\ApplicationDeviceController@destroy', ['application' => $this->application, 'device' => $this->device]))->assertForbidden();

		$this->assertDatabaseHas('electric_devices', [
			'id' => $this->device->id,
		]);
	}
}
