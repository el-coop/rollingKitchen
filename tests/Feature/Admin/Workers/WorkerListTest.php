<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkerListTest extends TestCase {
	use RefreshDatabase;

	protected $admin;
	protected $kitchen;
	protected $worker;

	protected function setUp() {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->worker = factory(Worker::class)->create();
		$this->worker->user()->save(factory(User::class)->make());

	}


	public function test_guest_cant_see_page() {
		$this->get(action('Admin\WorkerController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker->user)->get(action('Admin\WorkerController@index'))->assertForbidden();
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->user)->get(action('Admin\WorkerController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin->user)->get(action('Admin\WorkerController@index'))->assertSuccessful()->assertSee('</datatable>');
	}

	public function test_datatable_gets_data() {
		$workers = factory(Worker::class, 10)->create()->each(function($worker){
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



}