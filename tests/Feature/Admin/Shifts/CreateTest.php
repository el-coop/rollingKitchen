<?php

namespace Admin\Kitchens\Shifts;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Shift;
use App\Models\User;
use App\Models\Worker;
use App\Models\Workplace;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	protected $worker;
	private $shifts;

	protected function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->workplaces = Workplace::factory(10)->create();
		$this->shifts = Shift::factory(10)->make()->each(function ($shift) {
			$shift->workplace_id = $this->workplaces->random()->id;
			$shift->save();
		});

	}

	public function test_guest_cant_see_shift_form() {
		$this->get(action('Admin\ShiftController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_shift_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\ShiftController@create'))->assertForbidden();
	}

	public function test_worker_cant_see_shift_form() {
		$this->actingAs($this->worker)->get(action('Admin\ShiftController@create'))->assertForbidden();
	}

	public function test_accountant_cant_see_shift_form() {
		$this->actingAs($this->accountant)->get(action('Admin\ShiftController@create'))->assertForbidden();
	}

	public function test_admin_can_see_shift_form() {
		$this->actingAs($this->admin)->get(action('Admin\ShiftController@create'))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'date',
				'label' => __('admin/shifts.date'),
				'type' => 'text',
				'subType' => 'date',
				'value' => null
			])->assertJsonFragment([
				'name' => 'hours',
				'label' => __('admin/shifts.hours'),
				'type' => 'text',
				'subType' => 'number',
				'value' => null
			]);
	}

	public function test_guest_cant_create_a_shift() {
		$this->post(action('Admin\ShiftController@store'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_a_shift() {
		$this->actingAs($this->kitchen)->post(action('Admin\ShiftController@store'))->assertForbidden();
	}

	public function test_worker_cant_create_a_shift() {
		$this->actingAs($this->worker)->post(action('Admin\ShiftController@store'))->assertForbidden();
	}

	public function test_accountant_cant_create_a_shift() {
		$this->actingAs($this->accountant)->post(action('Admin\ShiftController@store'))->assertForbidden();
	}

	public function test_admin_can_create_a_shift() {
		$workplace = $this->workplaces->random();
		$date = Carbon::now();
		$this->actingAs($this->admin)->post(action('Admin\ShiftController@store'), [
			'date' => $date,
			'hours' => '10',
			'workplace' => $workplace->id
		])->assertSuccessful()->assertJsonFragment([
			'date' => $date,
			'hours' => '10',
			'name' => $workplace->name
		]);

		$this->assertDatabaseHas('shifts', [
			'date' => $date->format('Y-m-d'),
			'hours' => 10,
			'workplace_id' => $workplace->id,
			'closed' => false
		]);
	}

	public function test_create_shift_validation() {
		$this->actingAs($this->admin)->post(action('Admin\ShiftController@store'), [
			'date' => 'asd',
			'hours' => '0',
			'workplace' => 0
		])->assertRedirect()->assertSessionHasErrors([
			'date',
			'hours',
			'workplace',
		]);
	}
}
