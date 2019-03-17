<?php

namespace Tests\Feature\Admin\Kitchens;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Developer;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MotherlistTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	protected $worker;
	private $admin;
	protected $accountant;
	private $kitchens;
	private $developer;
	
	public function setUp(): void {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		factory(\App\Models\Field::class, 5)->create();
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->developer = factory(Developer::class)->create();
		$this->developer->user()->save(factory(User::class)->make());
		
		$this->kitchens = factory(Kitchen::class, 5)->create([
			'data' => function () {
				return Kitchen::fields()->mapWithKeys(function ($field) {
					$faker = $this->faker();
					if ($field->type === 'text') {
						$value = $faker->name;
					} else {
						$value = $faker->paragraph;
					}
					
					return [$field->name_en => $value];
					
				});
			},
		])->each(function ($kitchen) {
			$kitchen->user()->save(factory(User::class)->make());
		});
	}
	
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\KitchenController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\KitchenController@index'))->assertForbidden();
	}
	
	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchens->first()->user)->get(action('Admin\KitchenController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\KitchenController@index'))->assertForbidden();
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
	
	public function test_worker_cant_get_list() {
		$this->actingAs($this->worker)->get(action('DatatableController@list'))->assertForbidden();
	}
	
	
	public function test_kitchen_cant_get_list() {
		$this->actingAs($this->kitchens->first()->user)->get(action('DatatableController@list'))->assertForbidden();
	}

	public function test_accountant_cant_get_list() {
		$this->actingAs($this->accountant)->get(action('DatatableController@list'))->assertForbidden();
	}
	
	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.kitchensTable', 'per_page' => 20, 'sort' => 'name|asc']));
		
		$kitchens = array_values(Kitchen::all()->map(function ($kitchen) {
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
		$newKitchen = $this->kitchens->first();
		$newKitchen->status = 'new';
		$newKitchen->save();
		
		$response = $this->actingAs($this->admin->user)
			->get(action('DatatableController@list', ['table' => 'admin.kitchensTable', 'per_page' => 20, 'filter' => '{"status":"new"}']));
		
		$kitchens = $this->kitchens->filter(function ($kitchen) {
			return $kitchen->status == 'new';
		});
		$this->assertCount($kitchens->count(), $response->decodeResponseJson()['data']);
		
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
	
	public function test_worker_cant_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->worker)
			->get(action('Admin\KitchenController@edit', $kitchen))->assertForbidden();
	}
	
	public function test_kitchen_cant_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->kitchens->first()->user)
			->get(action('Admin\KitchenController@edit', $kitchen))->assertForbidden();
	}

	public function test_accountant_cant_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->accountant)
			->get(action('Admin\KitchenController@edit', $kitchen))->assertForbidden();
	}
	
	public function test_admin_can_get_kitchen_fields_with_values() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->get(action('Admin\KitchenController@edit', $kitchen))
			->assertJson($kitchen->fullData->toArray());
	}
	
	public function test_guest_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->patch(action('Admin\KitchenController@update', $kitchen))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->worker)
			->patch(action('Admin\KitchenController@update', $kitchen))->assertForbidden();
	}
	
	public function test_kitchen_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->kitchens->first()->user)
			->patch(action('Admin\KitchenController@update', $kitchen))->assertForbidden();
	}

	public function test_accountant_cant_edit_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->accountant)
			->patch(action('Admin\KitchenController@update', $kitchen))->assertForbidden();
	}
	
	public function test_admin_can_update_kitchen() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\KitchenController@update', $kitchen), [
				'name' => 'testname',
				'email' => 'bla@gla.gla',
				'language' => 'nl',
				'status' => 'motherlist',
				'kitchen' => [
					'test' => 'best',
					'jest' => 'rest',
				],
			])->assertSuccessful();
		
		$this->assertDatabaseHas('users', [
			'user_type' => Kitchen::class,
			'user_id' => $kitchen->id,
			'name' => 'testname',
			'email' => 'bla@gla.gla',
			'language' => 'nl',
		]);
		
		
		$this->assertDatabaseHas('kitchens', [
			'id' => $kitchen->id,
			'status' => 'motherlist',
			'data' => json_encode([
				'test' => 'best',
				'jest' => 'rest',
			]),
		]);
		
	}
	
	public function test_update_kitchen_validates() {
		$kitchen = $this->kitchens->random();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\KitchenController@update', $kitchen), [
				'name' => '',
				'email' => 'bla',
				'status' => 'zla',
			])->assertRedirect()->assertSessionHasErrors(['name', 'email', 'status']);
	}
	
	public function test_guest_cant_delete_kitchen() {
		$this->delete(action('Admin\KitchenController@destroy', $this->kitchens->first()))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_delete_another_kitchen() {
		$this->actingAs($this->worker)->delete(action('Admin\KitchenController@destroy', $this->kitchens->first()))->assertForbidden();
	}

	public function test_accountant_cant_delete_another_kitchen() {
		$this->actingAs($this->accountant)->delete(action('Admin\KitchenController@destroy', $this->kitchens->first()))->assertForbidden();
	}
	
	public function test_kitchen_cant_delete_another_kitchen() {
		$kitchen = Kitchen::find(2);
		$this->actingAs($kitchen->user)->delete(action('Admin\KitchenController@destroy', $this->kitchens->first()))->assertForbidden();
	}
	
	public function test_admin_can_delete_kitchen() {
		$kitchen = $this->kitchens->first();
		factory(Application::class, 2)->create([
			'kitchen_id' => $kitchen
		]);
		Storage::fake('local');
		$file = UploadedFile::fake()->image('photo.jpg');
		$file->store("public/photos");
		$kitchen->photos()->save(factory(Photo::class)->make([
			'file' => $file->hashName()
		]));
		
		$this->assertDatabaseHas('applications', ['kitchen_id' => $kitchen->id]);
		$this->assertDatabaseHas('photos', ['kitchen_id' => $kitchen->id]);
		Storage::assertExists("public/photos/{$file->hashName()}");
		
		$user = $kitchen->user;
		$this->actingAs($this->admin->user)->delete(action('Admin\KitchenController@destroy', $kitchen))->assertSuccessful();
		$this->assertDatabaseMissing('kitchens', ['id' => $kitchen->id]);
		$this->assertDatabaseMissing('users', ['id' => $user->id]);
		$this->assertDatabaseMissing('applications', ['kitchen_id' => $kitchen->id]);
		$this->assertDatabaseMissing('photos', ['kitchen_id' => $kitchen->id]);
		Storage::assertMissing("public/photos/{$file->hashName()}");
		
	}
	
	public function test_developer_can_delete_kitchen() {
		$kitchen = $this->kitchens->first();
		$user = $kitchen->user;
		$this->actingAs($this->developer->user)->delete(action('Admin\KitchenController@destroy', $kitchen))->assertSuccessful();
		$this->assertDatabaseMissing('kitchens', ['id' => $kitchen->id]);
		$this->assertDatabaseMissing('users', ['id' => $user->id]);
		
	}
	
}
