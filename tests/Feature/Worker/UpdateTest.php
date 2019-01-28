<?php

namespace Tests\Feature\Worker;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $kitchenPhoto;
	private $workerPhoto;
	
	protected function setUp() {
		parent::setUp();
		
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
	}
	
	public function test_guest_cant_see_worker_page() {
		$this->get(action('Worker\WorkerController@index', $this->worker->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}
	
	public function test_kitchen_cant_see_worker_page() {
		$this->actingAs($this->kitchen)->get(action('Worker\WorkerController@index', $this->worker->user))->assertForbidden();
	}
	
	public function test_other_worker_cant_see_worker_page() {
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
		$this->actingAs($worker)->get(action('Worker\WorkerController@index', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_can_see_own_page() {
		$this->actingAs($this->worker)->get(action('Worker\WorkerController@index', $this->worker->user))->assertSuccessful();
	}
	
	public function test_admin_can_see_worker_page() {
		$this->actingAs($this->admin)->get(action('Worker\WorkerController@index', $this->worker->user))->assertSuccessful();
	}
	
	public function test_guest_cant_update_worker() {
		$this->patch(action('Worker\WorkerController@update', $this->worker->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}
	
	public function test_kitchen_cant_update_worker() {
		$this->actingAs($this->kitchen)->patch(action('Worker\WorkerController@update', $this->worker->user))->assertForbidden();
	}
	
	public function test_other_worker_cant_update_worker() {
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
		$this->actingAs($worker)->patch(action('Worker\WorkerController@update', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_can_update_self() {
		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			]
		])->assertSessionHas('toast');
		
		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);
		
		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
			'data' => json_encode(['data'])
		]);
	}
	
	public function test_admin_can_update_worker() {
		$this->actingAs($this->admin)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			]
		])->assertSessionHas('toast');
		
		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);
		
		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
			'data' => json_encode(['data'])
		]);
	}
	
	public function test_worker_update_validation() {
		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla',
			'name' => 'g',
			'language' => '',
			'worker' => 'test'
		])->assertSessionHasErrors([
			'email','name','language','worker'
		]);
		
	}
}
