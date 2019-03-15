<?php

namespace Tests\Feature\Kitchens;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\User;
use App\Models\Worker;
use ElCoop\Valuestore\Valuestore;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationProductTest extends TestCase {
	use RefreshDatabase;

	protected $worker;
	private $admin;
	private $application;
	protected $accountant;
	private $kitchen;
	private $product;
	private $kitchen2;

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
		$settings->put('registration_year', 2018);

		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);

		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);

		$this->kitchen2 = factory(User::class)->make();
		$this->kitchen2->user()->save(factory(Kitchen::class)->create());

		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);

		$this->kitchen = factory(User::class)->make();
		$kitchen = factory(Kitchen::class)->create();
		$kitchen->user()->save($this->kitchen);

		$this->application = factory(Application::class)->make(['year' => app('settings')->get('registration_year')]);
		$kitchen->applications()->save($this->application);

		$this->product = factory(Product::class)->make();
		$this->application->products()->save($this->product);

	}

	public function test_a_guest_cant_create_product() {
		$this->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_a_different_worker_cant_create_product() {
		$this->actingAs($this->worker)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5
		])->assertForbidden();
	}

	public function test_a_different_kitchen_cant_create_product() {
		$this->actingAs($this->kitchen2)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5
		])->assertForbidden();
	}

	public function test_a_account_kitchen_cant_create_product() {
		$this->actingAs($this->accountant)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5
		])->assertForbidden();
	}

	public function test_admin_can_create_product() {
		$this->actingAs($this->admin)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5,
			'category' => 'menu'
		])->assertSuccessful();

		$this->assertDatabaseHas('products', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 2.5,
			'category' => 'menu'
		]);
	}

	public function test_kitchen_can_create_product_on_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5,
			'category' => 'menu'
		])->assertSuccessful();

		$this->assertDatabaseHas('products', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 2.5,
			'category' => 'menu'
		]);
	}

	public function test_worker_cant_create_product_on_submitted_application() {
		$this->actingAs($this->worker)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		])->assertForbidden();

		$this->assertDatabaseMissing('products', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		]);
	}

	public function test_accountant_cant_create_product_on_submitted_application() {
		$this->actingAs($this->accountant)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		])->assertForbidden();

		$this->assertDatabaseMissing('products', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		]);
	}

	public function test_kitchen_cant_create_product_on_submitted_application() {
		$this->actingAs($this->kitchen)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		])->assertForbidden();

		$this->assertDatabaseMissing('products', [
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 2.5,
			'category' => 'drinks'
		]);
	}

	public function test_product_name_price_validation() {
		$this->actingAs($this->admin)->post(action('Kitchen\ApplicationProductController@create', $this->application), [
			'name' => '',
			'price' => 'blaa',
			'category' => 'dr'
		])->assertSessionHasErrors(['name', 'price', 'category']);

		$this->assertDatabaseMissing('products', [
			'application_id' => $this->application->id,
			'name' => '',
			'price' => 'blaa'
		]);
	}

	public function test_a_guest_cant_edit_product() {
		$this->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_a_different_worker_cant_edit_product() {
		$this->actingAs($this->worker)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertForbidden();
	}

	public function test_a_accountant_cant_edit_product() {
		$this->actingAs($this->worker)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertForbidden();
	}

	public function test_a_different_kitchen_cant_edit_product() {
		$this->actingAs($this->kitchen2)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertForbidden();
	}


	public function test_admin_can_edit_product() {
		$this->actingAs($this->admin)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertSuccessful();

		$this->assertDatabaseHas('products', [
			'id' => $this->product->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 0.01
		]);
	}

	public function test_kitchen_can_edit_product_on_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertSuccessful();

		$this->assertDatabaseHas('products', [
			'id' => $this->product->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 0.01
		]);
	}


	public function test_kitchen_cant_edit_product_on_closed_application() {
		$this->actingAs($this->kitchen)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => 'test',
			'price' => 0.01
		])->assertForbidden();

		$this->assertDatabaseMissing('products', [
			'id' => $this->product->id,
			'application_id' => $this->application->id,
			'name' => 'test',
			'price' => 0.01
		]);
	}


	public function test_validation_product_edit() {
		$this->actingAs($this->admin)->patch(action('Kitchen\ApplicationProductController@update', ['application' => $this->application, 'product' => $this->product]), [
			'name' => '',
			'price' => 'gla'
		])->assertSessionHasErrors(['name', 'price']);

		$this->assertDatabaseMissing('products', [
			'id' => $this->product->id,
			'application_id' => $this->application->id,
			'name' => '',
			'price' => 'gla'
		]);
	}

	public function test_a_guest_cant_delete_product() {
		$this->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))
			->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_a_different_worker_cant_delete_product() {
		$this->actingAs($this->kitchen2)->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))
			->assertForbidden();
	}

	public function test_a_different_kitchen_cant_delete_product() {
		$this->actingAs($this->kitchen2)->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))
			->assertForbidden();
	}

	public function test_admin_can_delete_product() {
		$this->actingAs($this->admin)->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))->assertSuccessful();

		$this->assertDatabaseMissing('products', [
			'id' => $this->product->id,
		]);
	}

	public function test_kitchen_can_delete_product_on_open_application() {
		$this->application->status = 'new';
		$this->application->save();
		$this->actingAs($this->kitchen)->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))->assertSuccessful();

		$this->assertDatabaseMissing('products', [
			'id' => $this->product->id,
		]);
	}

	public function test_kitchen_cant_delete_product_on_closed_application() {
		$this->actingAs($this->kitchen)->delete(action('Kitchen\ApplicationProductController@destroy', ['application' => $this->application, 'product' => $this->product]))->assertForbidden();

		$this->assertDatabaseHas('products', [
			'id' => $this->product->id,
		]);
	}
}
