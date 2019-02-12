<?php

namespace Tests\Feature\Admin\Shifts;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Shift;
use App\Models\User;
use App\Models\Worker;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $workplaces;
	protected $worker;
	private $shifts;
	
	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->workplaces = factory(Workplace::class, 10)->create();
		$this->shifts = factory(Shift::class, 10)->make()->each(function ($shift) {
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
	
	public function test_admin_can_create_a_shift() {
		$this->withoutExceptionHandling();
		$workplace = $this->workplaces->random();
		$this->actingAs($this->admin)->post(action('Admin\ShiftController@store'), [
			'date' => '1/1/2029',
			'hours' => '10',
			'workplace' => $workplace->id
		])->assertSuccessful()->assertJsonFragment([
			'date' => '1/1/2029',
			'hours' => '10',
			'name' => $workplace->name
		]);
		
		$this->assertDatabaseHas('shifts', [
			'date' => '1/1/2029',
			'hours' => '10',
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
