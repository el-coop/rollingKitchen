<?php

namespace Tests\Feature\Admin\Kitchens;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MotherlistTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	private $admin;
	private $kitchens;
	
	public function setUp() {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		factory(\App\Models\Field::class, 5)->create();
		
		$this->kitchens = factory(Kitchen::class, 5)->create([
			'data' => function () {
				return Kitchen::fields()->mapWithKeys(function ($field) {
					$faker = $this->faker();
					if ($field->type === 'text') {
						$value = $faker->name;
					} else {
						$value = $faker->paragraph;
					}
					
					return [$field->name => $value];
					
				});
			}
		])->each(function ($kitchen) {
			$kitchen->user()->save(factory(User::class)->make());
		});
	}
	
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\KitchenController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	
	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchens->first()->user)->get(action('Admin\KitchenController@index'))->assertForbidden();
	}
	
	public function test_page_loads_with_datatable() {
		$this->actingAs($this->admin->user)->get(action('Admin\KitchenController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}
	
	public function test_datatable_gets_table_data() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.kitchensTable', 'per_page' => 20]));
		
		foreach ($this->kitchens as $kitchen) {
			$response->assertJsonFragment([
				'id' => $kitchen->id,
				'name' => $kitchen->user->name,
				'email' => $kitchen->user->email,
				'status' => $kitchen->status,
			]);
		}
	}
	
	public function test_guest_cant_get_list() {
		$this->get(action('DatatableController@list'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	
	public function test_kitchen_cant_get_list() {
		$this->actingAs($this->kitchens->first()->user)->get(action('DatatableController@list'))->assertForbidden();
	}
	
	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.kitchensTable', 'per_page' => 20, 'sort' => 'name|asc']));
		
		$kitchens = array_values($this->kitchens->map(function ($kitchen) {
			return [
				'count(kitchen_id)' => 0,
				'id' => $kitchen->id,
				'name' => $kitchen->user->name,
				'email' => $kitchen->user->email,
				'status' => $kitchen->status,
			];
		})->sortBy('name')->toArray());
		
		foreach ($response->json()['data'] as $key => $responseFragment) {
			$this->assertEquals($responseFragment['id'], $kitchens[$key]['id']);
		}
	}
	
	public function test_datatable_get_table_data_filtered() {
		$response = $this->actingAs($this->admin->user)
			->get(action('DatatableController@list', ['table' => 'admin.kitchensTable', 'per_page' => 20, 'filter' => '{"status":"new"}']));
		
		$kitchens = $this->kitchens->filter(function ($kitchen) {
			return $kitchen->status == 'new';
		});
		
		foreach ($kitchens as $kitchen) {
			$response->assertJsonFragment([
				'id' => $kitchen->id,
				'name' => $kitchen->user->name,
				'email' => $kitchen->user->email,
				'status' => $kitchen->status,
			]);
		}
	}
	
	
	public function test_guest_cant_get_kitchen_fields() {
		$kitchen = $this->kitchens->random();
		$this->get(action('Admin\KitchenController@edit', $kitchen))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	
	public function test_kitchen_cant_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->kitchens->first()->user)
			->get(action('Admin\KitchenController@edit', $kitchen))->assertForbidden();
	}
	
	public function test_can_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->get(action('Admin\KitchenController@edit', $kitchen))
			->assertJson($kitchen->fullData->toArray());
	}
	
	public function test_guest_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->patch(action('Admin\KitchenController@update', $kitchen))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	
	public function test_kitchen_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->kitchens->first()->user)
			->patch(action('Admin\KitchenController@update', $kitchen))->assertForbidden();
	}
	
	public function test_admin_can_update_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\KitchenController@update', $kitchen), [
				'name' => 'testname',
				'email' => 'bla@gla.gla',
				'status' => 'motherlist'
			])->assertSuccessful();
		
		$this->assertDatabaseHas('users', [
			'user_type' => Kitchen::class,
			'user_id' => $kitchen->id,
			'name' => 'testname',
			'email' => 'bla@gla.gla',
		]);
		
		
		$this->assertDatabaseHas('kitchens', [
			'id' => $kitchen->id,
			'status' => 'motherlist',
		]);
		
	}
	
	public function test_update_kitchen_validates() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\KitchenController@update', $kitchen), [
				'name' => '',
				'email' => 'bla',
				'status' => 'zla'
			])->assertRedirect()->assertSessionHasErrors(['name', 'email', 'status']);
	}
	
}
