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

	public function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);

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
		Worker::factory(10)->create([
			'approved' => true
		]);

		$this->actingAs($this->admin)->delete(action('Admin\WorkerController@disapprove'))->assertRedirect();

		$this->assertDatabaseMissing('workers', [
			'approved' => true
		]);
	}

}
