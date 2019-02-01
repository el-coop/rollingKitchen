<?php

namespace Tests\Feature\Worker;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupervisorTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $workplace;
	protected $supervisor;

	protected function setUp() {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->workplace = factory(Workplace::class)->create();
		factory(WorkFunction::class, 3)->make()->each(function ($workFunction) {
			$workplace = Workplace::first();
			$workplace->workFunctions()->save($workFunction);
		});
		$this->supervisor = factory(User::class)->make();
		factory(Worker::class)->create(['supervisor' => true])->user()->save($this->supervisor);
		$this->supervisor->user->workplaces()->attach($this->workplace);
		$this->worker->user->workplaces()->attach($this->workplace);
	}

	public function test_worker_cant_see_manage_workplace_tab() {
		$this->actingAs($this->worker)->get(action('Worker\WorkerController@index', $this->worker->user))
			->assertSuccessful()
			->assertDontSee(__('worker/supervisor.manageWorkers'));
	}

	public function test_supervisor_can_see_manage_workplace_tab() {
		$this->actingAs($this->supervisor)->get(action('Worker\WorkerController@index', $this->supervisor->user))
			->assertSuccessful()
			->assertSee(__('worker/supervisor.manageWorkers'));
	}

	public function test_guest_cant_get_workplace() {
		$this->get(action('Worker\SupervisorController@editWorkplace', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_workplace() {
		$this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@editWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_admin_cant_get_workplace() {
		$this->actingAs($this->admin)->get(action('Worker\SupervisorController@editWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_worker_cant_get_workplace() {
		$this->actingAs($this->worker)->get(action('Worker\SupervisorController@editWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_supervisor_can_get_workplace() {
		$response = $this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@editWorkplace', $this->workplace))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->workplace->name
			]);
		foreach ($this->workplace->workFunctions as $workFunction) {
			$response->assertJsonFragment([
				'id' => $workFunction->id,
				'name' => $workFunction->name,
				'payment_per_hour_before_tax' => $workFunction->payment_per_hour_before_tax,
				'payment_per_hour_after_tax' => $workFunction->payment_per_hour_after_tax
			]);
		};
	}

	public function test_guest_cant_update_workplace() {
		$this->patch(action('Worker\SupervisorController@updateWorkplace', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_workplace() {
		$this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@updateWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_admin_cant_update_workplace() {
		$this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_worker_cant_update_workplace() {
		$this->actingAs($this->worker)->patch(action('Worker\SupervisorController@updateWorkplace', $this->workplace))->assertForbidden();
	}

	public function test_supervisor_can_update_workplace() {
		$this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorkplace', [
			$this->workplace,
			'name' => 'new name']))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'new name',
				'id' => $this->workplace->id
			]);
		$this->assertDatabaseHas('workplaces', ['name' => 'new name', 'id' => $this->workplace->id]);

	}

	public function test_guest_cant_add_workFunction() {
		$this->post(action('Worker\SupervisorController@addWorkFunction', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_add_workFunction() {
		$this->actingAs($this->kitchen)->post(action('Worker\SupervisorController@addWorkFunction', $this->workplace))->assertForbidden();
	}

	public function test_admin_cant_add_workFunction() {
		$this->actingAs($this->admin)->post(action('Worker\SupervisorController@addWorkFunction', $this->workplace))->assertForbidden();
	}

	public function test_worker_cant_add_workFunction() {
		$this->actingAs($this->worker)->post(action('Worker\SupervisorController@addWorkFunction', $this->workplace))->assertForbidden();
	}

	public function test_supervisor_can_add_workFunction() {
		$response = $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkFunction', [
			$this->workplace,
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
		]))->assertSuccessful();
		$response->assertJsonFragment([
			'name' => 'name',
			'payment_per_hour_after_tax' => "10",
			'payment_per_hour_before_tax' => "12",
			'workplace_id' => 1
		]);

		$this->assertDatabaseHas('work_functions', [
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
			'workplace_id' => 1
		]);
	}

	public function test_guest_cant_update_workFunction() {
		$this->patch(action('Worker\SupervisorController@updateWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_workFunction() {
		$this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@updateWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_admin_cant_update_workFunction() {
		$this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_worker_cant_update_workFunction() {
		$this->actingAs($this->worker)->patch(action('Worker\SupervisorController@updateWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_supervisor_can_update_workFunction() {
		$response = $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorkFunction', [
			$this->workplace,
			$this->workplace->workFunctions->first(),
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
		]))->assertSuccessful();
		$response->assertJsonFragment([
			'name' => 'name',
			'payment_per_hour_after_tax' => "10",
			'payment_per_hour_before_tax' => "12",
			'id' => $this->workplace->workFunctions->first()->id,
		]);

		$this->assertDatabaseHas('work_functions', [
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
			'workplace_id' => 1,
			'id' => $this->workplace->workFunctions->first()->id,

		]);
	}

	public function test_guest_cant_delete_workFunction() {
		$this->delete(action('Worker\SupervisorController@destroyWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_workFunction() {
		$this->actingAs($this->kitchen)->delete(action('Worker\SupervisorController@destroyWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_admin_cant_delete_workFunction() {
		$this->actingAs($this->admin)->delete(action('Worker\SupervisorController@destroyWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_worker_cant_delete_workFunction() {
		$this->actingAs($this->worker)->delete(action('Worker\SupervisorController@destroyWorkFunction', [$this->workplace, $this->workplace->workfunctions->first()]))->assertForbidden();
	}

	public function test_supervisor_can_delete_workFunction() {
		$this->actingAs($this->supervisor)->delete(action('Worker\SupervisorController@destroyWorkFunction', [
			$this->workplace,
			$this->workplace->workFunctions->first(),
		]))->assertSuccessful();
		$this->assertDatabaseMissing('work_functions', [
			'id' => $this->workplace->workFunctions->first()->id,

		]);
	}

	public function test_guest_cant_create_worker() {
		$this->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_worker() {
		$this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
	}

	public function test_admin_cant_create_worker() {
		$this->actingAs($this->admin)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
	}

	public function test_worker_cant_create_worker() {
		$this->actingAs($this->worker)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
	}

	public function test_supervisor_can_create_worker() {
		$this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => null
			])->assertJsonFragment([
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => null
			]);
	}

	public function test_guest_cant_store_worker() {
		$this->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_store_worker() {
		$this->actingAs($this->kitchen)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
	}

	public function test_admin_cant_store_worker() {
		$this->actingAs($this->admin)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
	}

	public function test_worker_cant_store_worker() {
		$this->actingAs($this->worker)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
	}

	public function test_supervisor_can_store_worker() {
		$this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@storeWorker', [
			$this->workplace,
			'name' => 'name',
			'email' => 'test@test.com',
			'type' => 0,
			'language' => 'en']))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'email' => 'test@test.com',
				'type' => "0",
				'language' => 'en',
			]);

		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'test@test.com',
			'user_type' => Worker::class
		]);
	}

	public function test_guest_cant_get_supervisor_datatable() {
		$this->get(action('DatatableController@supervisorList', ['table' => json_encode($this->workplace->workersForSupervisor), 'per_page' => 20, 'sort' => 'name|asc']))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_admin_cant_get_supervisor_datatable() {
		$this->actingAs($this->admin)->get(action('DatatableController@supervisorList', ['table' => json_encode($this->workplace->workersForSupervisor), 'per_page' => 20, 'sort' => 'name|asc']))->assertForbidden();
	}

	public function test_kitchen_cant_get_supervisor_datatable() {
		$this->actingAs($this->kitchen)->get(action('DatatableController@supervisorList', ['table' => json_encode($this->workplace->workersForSupervisor), 'per_page' => 20, 'sort' => 'name|asc']))->assertForbidden();
	}

	public function test_worker_cant_get_supervisor_datatable() {
		$this->actingAs($this->worker)->get(action('DatatableController@supervisorList', ['table' => json_encode($this->workplace->workersForSupervisor), 'per_page' => 20, 'sort' => 'name|asc']))->assertForbidden();
	}

	public function test_supervisor_can_get_supervisor_datatable() {
		$table = str_replace('\\\\', '\\', json_encode($this->workplace->workersForSupervisor));
		$response = $this->actingAs($this->supervisor)->get(action('DatatableController@supervisorList', ['table' => $table, 'per_page' => 20, 'sort' => 'name|asc']))->assertSuccessful();
		$response->assertJsonFragment([
			'name' => $this->worker->name,
			'id' => $this->worker->user->id,
		]);
	}

	public function test_guest_cant_get_worker() {
		$this->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_worker() {
		$this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_admin_cant_get_worker() {
		$this->actingAs($this->admin)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_worker_cant_get_worker() {
		$this->actingAs($this->worker)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_supervisor_can_get_worker() {
		$this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@editWorker', [
			$this->workplace,
			$this->worker->user]))
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->worker->name,
			])->assertJsonFragment([
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->worker->email,
			]);
	}

	public function test_guest_cant_update_worker() {
		$this->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_worker() {
		$this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_admin_cant_update_worker() {
		$this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_worker_cant_update_worker() {
		$this->actingAs($this->worker)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
	}

	public function test_supervisor_can_update_worker() {
		$this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace,
			$this->worker->user]), [

			'name' => 'name',
			'email' => 'test@best.com',
			'type' => 1,
			'language' => 'en',
			'worker' => [
				'data' => 'bata',
			],
			'workplaces' => [$this->workplace->id],
		])->assertJsonFragment([
			'name' => 'name',
			'id' => $this->worker->user->id,
		]);
		$this->assertDatabaseHas('users', [
			'id' => $this->worker->id,
			'name' => 'name',
			'email' => 'test@best.com',
			'language' => 'en',
			'user_type' => Worker::class,
		]);

		$this->assertDatabaseHas('workers', [
			'supervisor' => false,
			'type' => 1,
			'data' => json_encode([
				'data' => 'bata',
			]),
		]);
	}
}
