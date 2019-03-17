<?php

namespace Tests\Feature\Admin\Services;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Developer;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class TableTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;

	protected $worker;
	private $admin;
	protected $accountant;
	private $kitchen;
	private $services;
	private $developer;

	public function setUp(): void {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());

		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());

		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);

		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);

		$this->services = factory(Service::class, 5)->create();

		$this->developer = factory(Developer::class)->create();
		$this->developer->user()->save(factory(User::class)->make());
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\ServiceController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\ServiceController@index'))->assertForbidden();
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->first()->user)->get(action('Admin\ServiceController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\ServiceController@index'))->assertForbidden();
	}

	public function test_page_loads_with_datatable() {
		$this->actingAs($this->admin->user)->get(action('Admin\ServiceController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_page_loads_with_datatable_developer() {
		$this->actingAs($this->developer->user)->get(action('Admin\ServiceController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.servicesTable', 'per_page' => 20, 'sort' => 'name|asc']));

		$services = array_values($this->services->map(function ($service) {
			return [
				'id' => $service->id,
				'name_nl' => $service->name_nl,
				'name_en' => $service->name_en,
				'type' => $service->type,
				'price' => $service,
			];
		})->sortBy('name')->toArray());

		foreach ($response->json()['data'] as $key => $responseFragment) {
			$this->assertEquals($responseFragment['id'], $services[$key]['id']);
		}
	}

	public function test_datatable_get_table_data_filtered() {
		$this->services->push(factory(Service::class)->create([
			'category' => 'safety',
		]));
		$response = $this->actingAs($this->admin->user)
			->get(action('DatatableController@list', ['table' => 'admin.servicesTable', 'per_page' => 20, 'filter' => '{"category":"safety"}']));

		$services = $this->services->filter(function ($service) {
			return $service->category == 'safety';
		});

		foreach ($services as $service) {
			$response->assertJsonFragment([
				'id' => $service->id,
				'name_nl' => $service->name_nl,
				'name_en' => $service->name_en,
				'category' => $service->category,
				'price' => (string)$service->price,
			]);
		}
	}

	public function test_guest_cant_edit_service() {
		$service = $this->services->random();
		$this->patch(action('Admin\ServiceController@update', $service))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_edit_service() {

		$service = $this->services->random();
		$this->actingAs($this->worker)
			->patch(action('Admin\ServiceController@update', $service))->assertForbidden();
	}

	public function test_kitchen_cant_edit_service() {

		$service = $this->services->random();
		$this->actingAs($this->kitchen->user)
			->patch(action('Admin\ServiceController@update', $service))->assertForbidden();
	}

	public function test_accountant_cant_edit_service() {

		$service = $this->services->random();
		$this->actingAs($this->accountant)
			->patch(action('Admin\ServiceController@update', $service))->assertForbidden();
	}

	public function test_guest_cant_get_service_fields() {
		$service = $this->services->random();
		$this->get(action('Admin\ServiceController@edit', $service))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_get_service_fields() {
		$service = $this->services->random();
		$this->actingAs($this->worker)->get(action('Admin\ServiceController@edit', $service))->assertForbidden();
	}

	public function test_kitchen_cant_get_service_fields() {
		$service = $this->services->random();
		$this->actingAs($this->kitchen->user)->get(action('Admin\ServiceController@edit', $service))->assertForbidden();
	}

	public function test_accountant_cant_get_service_fields() {
		$service = $this->services->random();
		$this->actingAs($this->accountant)->get(action('Admin\ServiceController@edit', $service))->assertForbidden();
	}

	public function test_can_admin_update_service() {
		$service = $this->services->random();

		$this->actingAs($this->admin->user)
			->patch(action('Admin\ServiceController@update', $service), [
				'name_nl' => 'testnaam',
				'name_en' => 'testname',
				'category' => 'safety',
				'type' => 0,
				'price' => '25.00',
			])->assertSuccessful();

		$this->assertDatabaseHas('services', [
			'id' => $service->id,
			'name_nl' => 'testnaam',
			'name_en' => 'testname',
			'category' => 'safety',
			'type' => 0,
			'price' => 25.00,
		]);
	}

	public function test_update_service_validates() {
		$service = $this->services->random();

		$this->actingAs($this->admin->user)
			->patch(action('Admin\ServiceController@update', $service), [
				'name_nl' => '',
				'name_en' => '',
				'category' => 'bla',
				'type' => '',
				'price' => 'zla',
			])->assertRedirect()->assertSessionHasErrors(['name_nl', 'name_en', 'category', 'type', 'price']);
	}

	public function test_can_admin_create_service() {

		$this->actingAs($this->admin->user)
			->post(action('Admin\ServiceController@create'), [
				'name_nl' => 'testnaam',
				'name_en' => 'testname',
				'category' => 'safety',
				'type' => 0,
				'price' => '25.00',
			])->assertSuccessful();

		$this->assertDatabaseHas('services', [
			'name_nl' => 'testnaam',
			'name_en' => 'testname',
			'category' => 'safety',
			'type' => 0,
			'price' => 25.00,
		]);
	}

	public function test_guest_cant_create_service() {

		$this->post(action('Admin\ServiceController@create'), [
			'name_nl' => 'testnaam',
			'name_en' => 'testname',
			'type' => 'safety',
			'price' => '25.00',
		])->assertRedirect(action('Auth\LoginController@login'));

	}

	public function test_worker_cant_create_service() {

		$this->actingAs($this->worker)
			->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'category' => 'safety',
				'type' => 0,
				'price' => '25.00',
			])->assertForbidden();

	}

	public function test_kitchen_cant_create_service() {

		$this->actingAs($this->kitchen->user)
			->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'category' => 'safety',
				'type' => 0,
				'price' => '25.00',
			])->assertForbidden();

	}

	public function test_accountant_cant_create_service() {

		$this->actingAs($this->accountant)
			->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'category' => 'safety',
				'type' => 0,
				'price' => '25.00',
			])->assertForbidden();

	}

	public function test_create_service_validates() {

		$this->actingAs($this->admin->user)
			->post(action('Admin\ServiceController@create'), [
				'name_nl' => '',
				'name_en' => '',
				'category' => 'bla',
				'type' => '',
				'price' => 'zla',
			])->assertRedirect()->assertSessionHasErrors(['name_nl', 'name_en', 'category', 'type', 'price']);
	}


	public function test_guest_cant_delete_service() {
		$this->delete(action('Admin\ServiceController@destroy', $this->services->first()))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_delete_service() {
		$this->actingAs($this->worker)->delete(action('Admin\ServiceController@destroy', $this->services->first()))->assertForbidden();
	}


	public function test_kitchen_cant_delete_service() {
		$this->actingAs($this->kitchen->user)->delete(action('Admin\ServiceController@destroy', $this->services->first()))->assertForbidden();
	}

	public function test_accountant_cant_delete_service() {
		$this->actingAs($this->accountant)->delete(action('Admin\ServiceController@destroy', $this->services->first()))->assertForbidden();
	}

	public function test_admin_can_delete_kitchen() {
		$service = $this->services->first();
		$this->actingAs($this->admin->user)->delete(action('Admin\ServiceController@destroy', $service))->assertSuccessful();
		$this->assertDatabaseMissing('services', ['id' => $service->id]);
		$this->assertDatabaseMissing('application_service', ['service_id' => $service->id]);

	}

	public function test_developer_can_delete_kitchen() {
		$service = $this->services->first();
		$this->actingAs($this->developer->user)->delete(action('Admin\ServiceController@destroy', $service))->assertSuccessful();
		$this->assertDatabaseMissing('services', ['id' => $service->id]);
		$this->assertDatabaseMissing('application_service', ['service_id' => $service->id]);

	}
}
