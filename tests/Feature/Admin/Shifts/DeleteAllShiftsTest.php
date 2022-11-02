<?php

namespace Tests\Feature\Admin\Shifts;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Shift;
use App\Models\User;
use App\Models\Worker;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteAllShiftsTest extends TestCase
{
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
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
	}

	public function test_guest_cant_delete_all_shifts() {
		$this->delete(action('Admin\ShiftController@deleteAll'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_all_shifts() {
		$this->actingAs($this->kitchen)->delete(action('Admin\ShiftController@deleteAll'))->assertForbidden();
	}

	public function test_worker_cant_delete_all_shifts() {
		$this->actingAs($this->worker)->delete(action('Admin\ShiftController@deleteAll'))->assertForbidden();
	}

	public function test_accountant_cant_delete_all_shifts() {
		$this->actingAs($this->accountant)->delete(action('Admin\ShiftController@deleteAll', $this->worker->user))->assertForbidden();
	}

	public function test_admin_can_delete_worker() {
		$workplace = Workplace::factory()->create();
        Shift::factory(10)->create([
			'workplace_id' => $workplace->id
		]);

		$this->actingAs($this->admin)->delete(action('Admin\ShiftController@deleteAll'))->assertRedirect();

		$this->assertCount(0, Shift::all());
	}
}
