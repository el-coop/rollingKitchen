<?php

namespace Tests\Feature\Worker;

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

class SupervisorExportShiftsTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $accountant;
	protected $workplace;
	protected $supervisor;
	protected $shift;
	protected $shiftWorker;
	
	protected function setUp(): void {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->workplace = factory(Workplace::class)->create();
		factory(WorkFunction::class, 3)->make()->each(function ($workFunction) {
			$workplace = Workplace::first();
			$workplace->workFunctions()->save($workFunction);
		});
		$this->supervisor = factory(User::class)->make();
		factory(Worker::class)->create(['supervisor' => true])->user()->save($this->supervisor);
		$this->supervisor->user->workplaces()->attach($this->workplace);
		$this->worker->user->workplaces()->attach($this->workplace);
		$this->shift = factory(Shift::class)->make([
			'hours' => 5
		]);
		$this->workplace->shifts()->save($this->shift);
		$this->shiftWorker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->shiftWorker);
		$this->shiftWorker->user->workplaces()->attach($this->workplace);
		$this->shift->workers()->attach($this->shiftWorker->user, [
			'start_time' => '20:00',
			'end_time' => '22:00',
			'work_function_id' => $this->workplace->workFunctions->first()->id
		]);
	}
	
	public function test_guest_cant_export_shifts() {
		$this->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_export_shifts() {
		$this->actingAs($this->kitchen)->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertForbidden();
	}
	
	public function test_admin_cant_export_shifts() {
		$this->actingAs($this->admin)->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertForbidden();
	}
	
	public function test_accountant_cant_export_shifts() {
		$this->actingAs($this->accountant)->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertForbidden();
	}
	
	public function test_worker_cant_export_shifts() {
		$this->actingAs($this->worker)->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertForbidden();
	}
	
	public function test_other_supervisor_cant_export_shifts() {
		$supervisor = factory(User::class)->make();
		factory(Worker::class)->create(['supervisor' => true])->user()->save($supervisor);
		$supervisor->user->workplaces()->attach(factory(Workplace::class)->create());
		$this->actingAs($supervisor)->post(action('Worker\SupervisorController@exportShifts', $this->workplace))->assertForbidden();
	}
	
	public function test_supervisor_can_export_shifts() {
		$this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@exportShifts', $this->workplace), [
			'days' => [
				0
			]
		])->assertSuccessful();
	}
}
