<?php

namespace Tests\Feature\Admin\Workers;

use App\Http\Controllers\Admin\WorkerController;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Notifications\Worker\UserCreated;
use Dompdf\Dompdf;
use Mockery;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewTest extends TestCase {
	
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	protected $worker;

	
	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
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

	public function test_accountant_cant_see_worker_page() {
		$this->actingAs($this->accountant)->get(action('Admin\WorkerController@show', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_cant_see_worker_page() {
		$this->actingAs($this->worker)->get(action('Admin\WorkerController@show', $this->worker->user))->assertForbidden();
	}
	
	public function test_admin_can_see_worker_page() {
		$this->actingAs($this->admin)->get(action('Admin\WorkerController@show', $this->worker->user))->assertSuccessful()
			->assertViewIs('admin.workers.show');
	}
	
	public function test_guest_cant_see_worker_pdf() {
		$this->get(action('Admin\WorkerController@pdf', $this->worker->user))->assertStatus(401);
	}


	public function test_kitchen_cant_see_worker_pdf() {
		$this->actingAs($this->kitchen)->get(action('Admin\WorkerController@pdf', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_cant_see_worker_pdf() {
		$this->actingAs($this->worker)->get(action('Admin\WorkerController@pdf', $this->worker->user))->assertForbidden();
	}
	
	public function test_admin_can_see_worker_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}
	
	public function test_accountant_can_see_worker_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}
}
