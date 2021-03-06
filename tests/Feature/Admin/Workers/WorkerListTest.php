<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkerListTest extends TestCase {
	use RefreshDatabase;
	
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $worker;
	private $workplaces;
	
	protected function setUp(): void {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);

	}
	
	
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\WorkerController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\WorkerController@index'))->assertForbidden();
	}
	
	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->user)->get(action('Admin\WorkerController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\WorkerController@index'))->assertForbidden();
	}
	
	public function test_admin_can_see_page() {
		$this->actingAs($this->admin->user)->get(action('Admin\WorkerController@index'))->assertSuccessful()->assertSee('</datatable>');
	}
	
	public function test_datatable_gets_data() {
		$workers = factory(Worker::class, 10)->create()->each(function ($worker) {
			$worker->user()->save(factory(User::class)->make());
			
		});
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.workersTable', 'per_page' => 20]));
		foreach ($workers as $worker) {
			$response->assertJsonFragment([
				'id' => $worker->id,
				'name' => $worker->user->name
			]);
		}
	}
	
	public function test_datatable_filters_by_name() {
		$workers = factory(Worker::class, 10)->create()->each(function ($worker) {
			$worker->user()->save(factory(User::class)->make());
			
		});
		
		$workerName = $workers->first()->user->name;
		
		$workersWithName = $workers->filter(function ($worker) use ($workerName) {
			return $worker->user->name == $workerName;
		});
		
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.workersTable', 'per_page' => 20, 'filter' => "{\"name\":\"{$workerName}\"}"]));
		$this->assertCount($workersWithName->count(), $response->decodeResponseJson()['data']);
		foreach ($workersWithName as $workerWithName) {
			$response->assertJsonFragment([
				'id' => $workerWithName->id,
				'name' => $workerWithName->user->name
			]);
		}
	}
	
	public function test_datatable_filters_by_workplace() {
		$workplaces = factory(Workplace::class, 10)->create();
		$workers = factory(Worker::class, 10)->create()->each(function ($worker) use ($workplaces) {
			$worker->user()->save(factory(User::class)->make());
			$worker->workplaces()->attach($workplaces->random(3));
		});
		
		$workplace = $workers->first()->workplaces()->first();
		
		$workersWithWorkplace = $workers->filter(function ($worker) use ($workplace) {
			return $worker->workplaces()->where('workplace_id', $workplace->id)->first();
		});
		
		$response = $this->actingAs($this->admin->user)->get(action('DatatableController@list', ['table' => 'admin.workersTable', 'per_page' => 20, 'filter' => "{\"workplacesList\":\"{$workplace->name}\"}"]));
		$this->assertCount($workersWithWorkplace->count(), $response->decodeResponseJson()['data']);
		foreach ($workersWithWorkplace as $workerWithWorkplace) {
			$response->assertJsonFragment([
				'id' => $workerWithWorkplace->id,
				'name' => $workerWithWorkplace->user->name
			]);
		}
	}
	
}
