<?php

namespace Tests\Feature\Admin\Shifts;

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
	private $shift;
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
		$this->shift = factory(Shift::class)->create([
			'workplace_id' => factory(Workplace::class)->create()->id
		]);
	}
	
	public function test_guest_cant_delete_shift() {
		$this->delete(action('Admin\ShiftController@destroy', $this->shift))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_delete_shift() {
		$this->actingAs($this->kitchen)->delete(action('Admin\ShiftController@destroy', $this->shift))->assertForbidden();
	}
	
	public function test_worker_cant_delete_shift() {
		$this->actingAs($this->worker)->delete(action('Admin\ShiftController@destroy', $this->shift))->assertForbidden();
	}
	
	public function test_accountant_cant_delete_shift() {
		$this->actingAs($this->accountant)->delete(action('Admin\ShiftController@destroy', $this->shift))->assertForbidden();
	}
	
	public function test_admin_can_delete_shift() {
		
		$this->shift->workers()->attach($this->worker->user,[
			'start_time' => '00:00',
			'end_time' => '10:00',
			'work_function_id' => factory(WorkFunction::class)->create([
				'workplace_id' => $this->shift->workplace_id
			])->id
		]);
		
		$this->actingAs($this->admin)->delete(action('Admin\ShiftController@destroy', $this->shift))->assertSuccessful();
		
		$this->assertDatabaseMissing('shifts', [
			'id' => $this->shift->id
		]);
		
		$this->assertDatabaseMissing('shift_worker', [
			'shift_id' => $this->shift->id,
			'worker_id' => $this->worker->user->id
		]);
	}
}
