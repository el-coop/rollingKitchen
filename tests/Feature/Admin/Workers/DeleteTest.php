<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Shift;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $accountant;
	
	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		
	}
	
	public function test_guest_cant_delete_worker() {
		$this->delete(action('Admin\WorkerController@destroy', $this->worker->user))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_delete_worker() {
		$this->actingAs($this->kitchen)->delete(action('Admin\WorkerController@destroy', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_cant_delete_worker() {
		$this->actingAs($this->worker)->delete(action('Admin\WorkerController@destroy', $this->worker->user))->assertForbidden();
	}
	
	public function test_accountant_cant_delete_worker() {
		$this->actingAs($this->accountant)->delete(action('Admin\WorkerController@destroy', $this->worker->user))->assertForbidden();
	}
	
	public function test_admin_can_delete_worker() {
		factory(Workplace::class)->create()->each(function ($workplace) {
			factory(WorkFunction::class)->make()->each(function ($workFunction) use ($workplace) {
				$workplace->workFunctions()->save($workFunction);
			});
			factory(Shift::class)->make()->each(function ($shift) use ($workplace) {
				$workplace->shifts()->save($shift);
				$shift->workers()->attach($this->worker, ['start_time' => '00:00', 'end_time' => '01:00', 'work_function_id' => $workplace->workFunctions()->first()->id]);
			});
		});
		
		$this->actingAs($this->admin)->delete(action('Admin\WorkerController@destroy', $this->worker->user))->assertSuccessful();
		
		$this->assertDatabaseMissing('workers', [
			'id' => $this->worker->user->id
		]);
		
		$this->assertDatabaseMissing('users', [
			'id' => $this->worker->id
		]);
		
		$this->assertDatabaseMissing('shift_worker', [
			'id' => $this->worker->user->id
		]);
	}
	
}
