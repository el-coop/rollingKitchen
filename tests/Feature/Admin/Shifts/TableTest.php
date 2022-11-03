<?php

namespace Admin\Kitchens\Shifts;

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

class TableTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	private $worker;
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

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\ShiftController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\ShiftController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\ShiftController@index'))->assertForbidden();
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\ShiftController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin)->get(action('Admin\ShiftController@index'))->assertSuccessful()->assertSee('</datatable>', false);
	}

	public function test_datatable_gets_data() {
		$response = $this->actingAs($this->admin)->get(action('DatatableController@list', ['table' => 'admin.shiftsTable', 'per_page' => 20]));
		foreach ($this->shifts as $shift) {
			$response->assertJsonFragment([
				'id' => $shift->id,
				'date' => $shift->date
			]);
		}
	}
}
