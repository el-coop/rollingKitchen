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

class UpdateTest extends TestCase {
	use RefreshDatabase;

	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	protected $worker;

	public function setUp(): void {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(Worker::class)->create();
		$this->worker->user()->save(factory(User::class)->make());
		$this->workplaces = factory(Workplace::class, 10)->create();


	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\WorkerController@edit', $this->worker))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_guest_cant_update_worker() {
		$this->patch(action('Admin\WorkerController@update', $this->worker))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker->user)->get(action('Admin\WorkerController@edit', $this->worker))->assertForbidden();
	}

	public function test_worker_cant_update_worker() {
		$this->actingAs($this->worker->user)->patch(action('Admin\WorkerController@update', $this->worker))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\WorkerController@edit', $this->worker))->assertForbidden();
	}

	public function test_accountant_cant_update_worker() {
		$this->actingAs($this->accountant)->patch(action('Admin\WorkerController@update', $this->worker))->assertForbidden();
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->user)->get(action('Admin\WorkerController@edit', $this->worker))->assertForbidden();
	}

	public function test_kitchen_cant_update_worker() {
		$this->actingAs($this->kitchen->user)->patch(action('Admin\WorkerController@update', $this->worker))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin->user)->get(action('Admin\WorkerController@edit', $this->worker))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => $this->worker->user->name,
			])->assertJsonFragment([
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => $this->worker->user->email,
			]);
	}

	public function test_admin_can_update_worker() {

		$workplaces = $this->workplaces->random(2)->pluck('id');
		$this->actingAs($this->admin->user)->patch(action('Admin\WorkerController@update', $this->worker), [
			'name' => 'name',
			'email' => 'test@best.com',
			'type' => 1,
			'language' => 'en',
			'worker' => [
				'data' => 'bata',
			],
			'approved' => true,
			'workplaces' => $workplaces->toArray(),
		])->assertSuccessful();

		$this->assertDatabaseHas('users', [
			'id' => $this->worker->user->id,
			'name' => 'name',
			'email' => 'test@best.com',
			'language' => 'en',
			'user_type' => Worker::class,
		]);

		$this->assertDatabaseHas('workers', [
			'supervisor' => 0,
			'type' => 1,
			'approved' => 1,
			'data' => json_encode([
				'data' => 'bata',
			]),
		]);

		foreach ($workplaces as $workplace) {
			$this->assertDatabaseHas('worker_workplace', [
				'worker_id' => $this->worker->id,
				'workplace_id' => $workplace,
			]);
		}
	}

	public function test_update_worker_validation() {
		$this->actingAs($this->admin->user)->patch(action('Admin\WorkerController@update', $this->worker), [
			'name' => '',
			'email' => 'test',
			'type' => 3,
			'language' => 'dl',
			'supervisor' => 'asd',
			'approved' => 'asd',
			'workplaces' => 'asd'
		])->assertRedirect()->assertSessionHasErrors([
			'name',
			'email',
			'type',
			'language',
			'supervisor',
			'approved',
			'worker',
			'workplaces'
		]);
	}

	public function test_guest_cant_non_ajax_update_worker() {
		$this->patch(action('Admin\WorkerController@nonAjaxUpdate', $this->worker))->assertRedirect(action('Auth\LoginController@login'));
	}


	public function test_worker_cant_non_ajax_update_worker() {
		$this->actingAs($this->worker->user)->patch(action('Admin\WorkerController@nonAjaxUpdate', $this->worker))->assertForbidden();
	}



	public function test_accountant_cant_non_ajax_update_worker() {
		$this->actingAs($this->accountant)->patch(action('Admin\WorkerController@nonAjaxUpdate', $this->worker))->assertForbidden();
	}


	public function test_kitchen_cant_non_ajax_update_worker() {
		$this->actingAs($this->kitchen->user)->patch(action('Admin\WorkerController@nonAjaxUpdate', $this->worker))->assertForbidden();
	}

	public function test_admin_can_non_ajax_update_worker() {

		$workplaces = $this->workplaces->random(2)->pluck('id');
		$this->actingAs($this->admin->user)->patch(action('Admin\WorkerController@nonAjaxUpdate', $this->worker), [
			'name' => 'name',
			'email' => 'test@best.com',
			'type' => 1,
			'language' => 'en',
			'worker' => [
				'data' => 'bata',
			],
			'approved' => true,
			'workplaces' => $workplaces->toArray(),
		])->assertRedirect();

		$this->assertDatabaseHas('users', [
			'id' => $this->worker->user->id,
			'name' => 'name',
			'email' => 'test@best.com',
			'language' => 'en',
			'user_type' => Worker::class,
		]);

		$this->assertDatabaseHas('workers', [
			'supervisor' => 0,
			'type' => 1,
			'approved' => 1,
			'data' => json_encode([
				'data' => 'bata',
			]),
		]);

		foreach ($workplaces as $workplace) {
			$this->assertDatabaseHas('worker_workplace', [
				'worker_id' => $this->worker->id,
				'workplace_id' => $workplace,
			]);
		}
	}
}
