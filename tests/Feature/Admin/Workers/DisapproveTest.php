<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DisapproveTest extends TestCase {

	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $accountant;

	public function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);

	}

	public function test_guest_cant_disapprove_worker() {
		$this->delete(action('Admin\WorkerController@disapprove'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_disapprove_worker() {
		$this->actingAs($this->kitchen)->delete(action('Admin\WorkerController@disapprove'))->assertForbidden();
	}

	public function test_accountant_cant_disapprove_worker() {
		$this->actingAs($this->accountant)->delete(action('Admin\WorkerController@disapprove'))->assertForbidden();
	}

	public function test_worker_cant_disapprove_worker() {
		$this->actingAs($this->worker)->delete(action('Admin\WorkerController@disapprove'))->assertForbidden();
	}

	public function test_admin_can_disapprove_worker() {
		factory(Worker::class, 10)->create([
			'approved' => true
		]);

		$this->actingAs($this->admin)->delete(action('Admin\WorkerController@disapprove'))->assertRedirect();

		$this->assertDatabaseMissing('workers', [
			'approved' => true
		]);
	}

}