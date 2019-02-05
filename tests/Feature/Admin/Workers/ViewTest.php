<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Notifications\Worker\UserCreated;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewTest extends TestCase {
	
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $workplaces;
	protected $worker;
	
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
		$this->get(action('Admin\WorkerController@show', $this->worker->user))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_see_worker_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\WorkerController@show', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_cant_see_worker_page() {
		$this->actingAs($this->worker)->get(action('Admin\WorkerController@show', $this->worker->user))->assertForbidden();
	}
	
	public function test_admin_can_see_worker_page() {
		$this->actingAs($this->admin)->get(action('Admin\WorkerController@show', $this->worker->user))->assertSuccessful()
			->assertViewIs('admin.workers.show');
	}
}
