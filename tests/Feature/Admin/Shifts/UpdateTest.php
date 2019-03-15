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

class UpdateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplace;
	protected $worker;
	private $shift;

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
		$this->workplace = factory(Workplace::class)->create();
		$this->shift = factory(Shift::class)->create([
			'workplace_id' => $this->workplace->id,
		]);

	}

	public function test_guest_cant_see_shift_update_page() {
		$this->get(action('Admin\ShiftController@edit', $this->shift))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_shift_update_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\ShiftController@edit', $this->shift))->assertForbidden();
	}

	public function test_accountant_cant_see_shift_update_page() {
		$this->actingAs($this->accountant)->get(action('Admin\ShiftController@edit', $this->shift))->assertForbidden();
	}

	public function test_worker_cant_see_shift_update_page() {
		$this->actingAs($this->worker)->get(action('Admin\ShiftController@edit', $this->shift))->assertForbidden();
	}

	public function test_admin_can_see_shift_update_page() {
		$this->actingAs($this->admin)->get(action('Admin\ShiftController@edit', $this->shift))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'date',
				'label' => __('admin/shifts.date'),
				'type' => 'text',
				'subType' => 'date',
				'value' => $this->shift->date,
			])->assertJsonFragment([
				'name' => 'workplace',
				'label' => __('worker/worker.workplace'),
				'type' => 'select',
				'options' => Workplace::select('id', 'name')->get()->pluck('name', 'id'),
				'value' => "{$this->shift->workplace_id}",
			]);
	}

	public function test_guest_cant_update_shift() {
		$this->patch(action('Admin\ShiftController@update', $this->shift))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_update_shift() {
		$this->actingAs($this->kitchen)->patch(action('Admin\ShiftController@update', $this->shift))->assertForbidden();

	}
	public function test_accountant_cant_update_shift() {
		$this->actingAs($this->accountant)->patch(action('Admin\ShiftController@update', $this->shift))->assertForbidden();

	}
	public function test_worker_cant_update_shift() {
		$this->actingAs($this->worker)->patch(action('Admin\ShiftController@update', $this->shift))->assertForbidden();

	}

	public function test_admin_can_update_shift() {
		$workplace = factory(Workplace::class)->create();
		$this->actingAs($this->admin)->patch(action('Admin\ShiftController@update', $this->shift), [
			'date' => '2019-9-2',
			'workplace' => $workplace->id,
			'hours' => 1
		])->assertSuccessful();

		$this->assertDatabaseHas('shifts', [
			'id' => $this->shift->id,
			'date' => '2019-9-2',
			'workplace_id' => $workplace->id,
			'hours' => 1
		]);

	}

	public function test_update_shift_page_validation() {
		$this->actingAs($this->admin)->patch(action('Admin\ShiftController@update', $this->shift), [
			'date' => '',
			'workplace' => '',
			'hours' =>'',

		])->assertRedirect()->assertSessionHasErrors([
			'date',
			'workplace',
			'hours'

		]);
	}

}
