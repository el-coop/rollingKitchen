<?php

namespace Tests\Feature\Admin\Kitchens;

use App\Http\Controllers\Admin\ApplicationController;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	protected $worker;
	private $admin;
	private $accountant;
	private $applications;
	private $kitchens;
	
	protected function setUp(): void {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		factory(\App\Models\Field::class, 5)->create(['form' => Application::class,]);
		$this->applications = collect();
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
			}
		])->each(function ($kitchen) {
			$kitchen->user()->save(factory(User::class)->make());
			for ($i = 0; $i < 2; $i++) {
				$kitchen->applications()->save(factory(Application::class)->make(['year' => 2016 + $i]));
			}
		});
		$this->applications = Application::all();
	}
	
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\ApplicationController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\ApplicationController@index'))->assertForbidden();
	}
	
	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\ApplicationController@index'))->assertForbidden();
	}
	
	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchens->first()->user)->get(action('Admin\ApplicationController@index'))->assertForbidden();
	}
	
	public function test_admin_can_see_page() {
		$this->actingAs($this->admin->user)->get(action('Admin\ApplicationController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}
	
	public function test_datatable_gets_table_data() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.applicationsTable', 'per_page' => 20]));
		foreach ($this->applications as $application) {
			$response->assertJsonFragment([
				'id' => $application->id,
				'name' => $application->kitchen->user->name,
				'year' => $application->year,
				'status' => $application->status,
			]);
		}
	}
	
	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.applicationsTable', 'per_page' => 20, 'sort' => 'year|asc']));
		
		$applications = array_values($this->applications->map(function ($application) {
			return [
				'id' => $application->id,
				'year' => $application->year,
				'status' => $application->status,
			];
		})->sortBy('year')->toArray());
		
		foreach ($response->json()['data'] as $key => $responseFragment) {
			$this->assertEquals($responseFragment['id'], $applications[$key]['id']);
		}
	}
	
	public function test_datatable_get_table_data_filtered() {
		$response = $this->actingAs($this->admin->user)
			->get(action('DatatableController@list', ['table' => 'admin.applicationsTable', 'per_page' => 20, 'filter' => '{"status":"rejected"}']));
		
		$applications = $this->applications->filter(function ($kitchen) {
			return $kitchen->status == 'rejected';
		});
		
		foreach ($applications as $application) {
			$response->assertJsonFragment([
				'id' => $application->id,
				'year' => $application->year,
				'status' => $application->status,
			]);
		}
	}
	
	public function test_guest_cant_get_application_fields() {
		$application = $this->applications->random();
		$this->get(action('Admin\ApplicationController@edit', $application))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_get_application_fields_with_values() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)
			->get(action('Admin\ApplicationController@edit', $application))->assertForbidden();
	}
	
	public function test_accountant_cant_get_application_fields_with_values() {
		$application = $this->applications->random();
		$this->actingAs($this->accountant)
			->get(action('Admin\ApplicationController@edit', $application))->assertForbidden();
	}
	
	public function test_kitchen_cant_get_application_fields_with_values() {
		$application = $this->applications->random();
		$this->actingAs($this->kitchens->first()->user)
			->get(action('Admin\ApplicationController@edit', $application))->assertForbidden();
	}
	
	public function test_admin_can_get_kitchen_fields_with_values() {
		$application = $this->applications->random();
		$this->actingAs($this->admin->user)
			->get(action('Admin\ApplicationController@edit', $application))
			->assertJson($application->fullData->toArray());
	}
	
	public function test_guest_cant_update_application() {
		$application = $this->applications->random();
		$this->patch(action('Admin\ApplicationController@update', $application))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_guest_cant_update_application_dimensions() {
		$application = $this->applications->random();
		$this->patch(action('Admin\ApplicationController@updateDimensions', $application))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_update_application() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)->patch(action('Admin\ApplicationController@update', $application))->assertForbidden();
	}
	
	public function test_worker_cant_update_application_dimensions() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)->patch(action('Admin\ApplicationController@updateDimensions', $application))->assertForbidden();
	}
	
	public function test_accountant_cant_update_application() {
		$application = $this->applications->random();
		$this->actingAs($this->accountant)->patch(action('Admin\ApplicationController@update', $application))->assertForbidden();
	}
	
	public function test_accountant_cant_update_application_dimensions() {
		$application = $this->applications->random();
		$this->actingAs($this->accountant)->patch(action('Admin\ApplicationController@updateDimensions', $application))->assertForbidden();
	}
	
	public function test_kitchen_cant_update_application() {
		$application = $this->applications->random();
		$this->actingAs($this->kitchens->first()->user)->patch(action('Admin\ApplicationController@update', $application))->assertForbidden();
	}
	
	public function test_kitchen_cant_update_application_dimensions() {
		$application = $this->applications->random();
		$this->actingAs($this->kitchens->first()->user)->patch(action('Admin\ApplicationController@updateDimensions', $application))->assertForbidden();
	}
	
	public function test_admin_can_update_application() {
		$application = $this->applications->random();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\ApplicationController@update', $application), [
				'year' => 2014,
				'status' => 'pending',
				'application' => [
					'test' => 'best',
					'rest' => 'quest'
				]
			])->assertSuccessful();
		
		$this->assertDatabaseHas('applications', [
			'id' => $application->id,
			'year' => 2014,
			'status' => 'pending',
		]);
		$updatedAplication = Application::find($application->id);
		$this->assertEquals(collect([
            'test' => 'best',
            'rest' => 'quest'
        ]), $updatedAplication->data);
	}
	
	public function test_admin_can_update_application_dimensions() {
		$application = $this->applications->first();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\ApplicationController@updateDimensions', $application), [
				'length' => 10,
				'width' => 20,
				'terrace_length' => 10,
				'terrace_width' => 20,
			])->assertSuccessful();
		
		$this->assertDatabaseHas('applications', [
			'id' => $application->id,
			'length' => '10',
			'width' => '20',
			'terrace_length' => '10',
			'terrace_width' => '20',
		]);
	}
	
	public function test_guest_cant_see_application_page() {
		$application = $this->applications->random();
		$this->get(action('Admin\ApplicationController@show', $application))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_see_application_page() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)->get(action('Admin\ApplicationController@show', $application))->assertForbidden();
	}
	
	public function test_accountant_cant_see_application_page() {
		$application = $this->applications->random();
		$this->actingAs($this->accountant)->get(action('Admin\ApplicationController@show', $application))->assertForbidden();
	}
	
	public function test_kitchen_cant_see_application_page() {
		$application = $this->applications->random();
		$this->actingAs($this->kitchens->first()->user)->get(action('Admin\ApplicationController@show', $application))->assertForbidden();
	}
	
	public function test_admin_can_see_application_page() {
		$application = $this->applications->random();
		$applicationIndex = $application->kitchen->applications()->orderBy('year', 'desc')->get()->search(function ($item) use ($application) {
			return $application->year == $item->year;
		});
		
		$this->actingAs($this->admin->user)->get(action('Admin\ApplicationController@show', $application))->assertRedirect(action('Admin\KitchenController@show', [
			'kitchen' => $application->kitchen,
			'tab' => __('admin/applications.applications'),
			'application' => $applicationIndex
		]));
	}
	
	// Application Services
	
	public function test_guest_cant_update_application_services() {
		$application = $this->applications->random();
		$this->patch(action('Admin\ApplicationController@updateServices', $application))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_update_services() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)->patch(action('Admin\ApplicationController@updateServices', $application))->assertForbidden();
	}
	
	public function test_worker_cant_update_application_services() {
		$application = $this->applications->random();
		$this->actingAs($this->worker)->patch(action('Admin\ApplicationController@updateServices', $application))->assertForbidden();
	}
	
	public function test_accountant_cant_update_application_services() {
		$application = $this->applications->random();
		$this->actingAs($this->accountant)->patch(action('Admin\ApplicationController@updateServices', $application))->assertForbidden();
	}
	
	
	public function test_kitchen_cant_update_application_services() {
		$application = $this->applications->random();
		$this->actingAs($this->kitchens->first()->user)->patch(action('Admin\ApplicationController@updateServices', $application))->assertForbidden();
	}
	
	
	public function test_admin_can_update_application_services() {
		$services = factory(Service::class, 3)->create();
		
		$application = $this->applications->first();
		$this->actingAs($this->admin->user)
			->patch(action('Admin\ApplicationController@updateServices', $application), ['services' => [
				$services->get(0)->id => 1,
				$services->get(1)->id => 0,
				$services->get(2)->id => 5
			]])->assertSuccessful();
		
		$this->assertDatabaseMissing('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(1)->id,
		]);
		
		$this->assertDatabaseHas('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(0)->id,
			'quantity' => 1
		]);
		$this->assertDatabaseHas('application_service', [
				'application_id' => $application->id,
				'service_id' => $services->get(2)->id,
				'quantity' => 5
			]
		);
	}
}
