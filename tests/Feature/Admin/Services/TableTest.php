<?php

namespace Tests\Feature\Admin\Services;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class TableTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;

	private $admin;
	private $kitchen;
	private $services;


	public function setUp() {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());

		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());

		$this->services = factory(Service::class, 5)->create();
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\ServiceController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->first()->user)->get(action('Admin\ServiceController@index'))->assertForbidden();
	}

	public function test_page_loads_with_datatable() {
		$this->actingAs($this->admin->user)->get(action('Admin\ServiceController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.servicesTable', 'per_page' => 20, 'sort' => 'name|asc']));

		$services = array_values($this->services->map(function ($service) {
			return [
				'id' => $service->id,
				'name' => $service->name,
				'type' => $service->type,
				'price' => $service,
			];
		})->sortBy('name')->toArray());

		foreach ($response->json()['data'] as $key => $responseFragment) {
			$this->assertEquals($responseFragment['id'], $services[$key]['id']);
		}
	}

	public function test_datatable_get_table_data_filtered() {
		$response = $this->actingAs($this->admin->user)
			->get(action('DatatableController@list', ['table' => 'admin.servicesTable', 'per_page' => 20, 'filter' => '{"type":"safety"}']));

		$services = $this->services->filter(function ($service) {
			return $service->type == 'safety';
		});

		foreach ($services as $service) {
			$response->assertJsonFragment([
				'id' => $service->id,
				'name' => $service->name,
				'type' => $service->type,
				'price' => (string)$service->price,
			]);
		}
	}

	public function test_guest_cant_edit_service() {
		$service = $this->services->random();
		$this->patch(action('Admin\ServiceController@update', $service))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_edit_service() {

		$service = $this->services->random();
		$this->actingAs($this->kitchen->user)
			->patch(action('Admin\ServiceController@update', $service))->assertForbidden();
	}

	public function test_guest_cant_get_service_fields() {
		$service = $this->services->random();
		$this->get(action('Admin\ServiceController@edit', $service))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_service_fields() {
		$service = $this->services->random();
		$this->actingAs($this->kitchen->user)->get(action('Admin\ServiceController@edit', $service))->assertForbidden();
	}

	public function test_can_admin_update_service() {
		$service = $this->services->random();

		$this->actingAs($this->admin->user)
			->patch(action('Admin\ServiceController@update', $service), [
				'name' => 'testname',
				'type' => 'safety',
				'price' => '25.00'
			])->assertSuccessful();

		$this->assertDatabaseHas('services', [
			'id' => $service->id,
			'name' => 'testname',
			'type' => 'safety',
			'price' => 25.00
		]);
	}

	public function test_update_service_validates() {
		$service = $this->services->random();

		$this->actingAs($this->admin->user)
			->patch(action('Admin\ServiceController@update', $service), [
				'name' => '',
				'type' => 'bla',
				'price' => 'zla'
			])->assertRedirect()->assertSessionHasErrors(['name', 'type', 'price']);
	}

	public function test_can_admin_create_service() {

		$this->actingAs($this->admin->user)
			->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'type' => 'safety',
				'price' => '25.00'
			])->assertSuccessful();

		$this->assertDatabaseHas('services', [
			'name' => 'testname',
			'type' => 'safety',
			'price' => 25.00
		]);
	}

	public function test_guest_cant_create_service() {

		$this->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'type' => 'safety',
				'price' => '25.00'
			])->assertRedirect(action('Auth\LoginController@login'));

	}
	public function test_kitchen_cant_create_service() {

		$this->actingAs($this->kitchen->user)
			->post(action('Admin\ServiceController@create'), [
				'name' => 'testname',
				'type' => 'safety',
				'price' => '25.00'
			])->assertForbidden();

	}

	public function test_create_service_validates() {

		$this->actingAs($this->admin->user)
			->post(action('Admin\ServiceController@create'), [
				'name' => '',
				'type' => 'bla',
				'price' => 'zla'
			])->assertRedirect()->assertSessionHasErrors(['name', 'type', 'price']);
	}

}