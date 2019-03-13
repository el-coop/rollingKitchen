<?php

namespace Tests\Feature\Admin\WorkedHoursExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $worker;

	protected function setUp() {
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

	public function test_guest_cant_create_worked_hours_export_column() {
		$this->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_worked_hours_export_column() {
		$this->actingAs($this->kitchen)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertForbidden();
	}

	public function test_worker_cant_create_worked_hours_export_column() {
		$this->actingAs($this->worker)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertForbidden();
	}

	public function test_accountant_cant_create_worked_hours_export_column() {
		$this->actingAs($this->accountant)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertForbidden();
	}

	public function test_admin_can_create_worked_hours_export_column() {
		$this->actingAs($this->admin)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'column' => 'worker.type'
			]);
		$this->assertDatabaseHas('worked_hours_export_columns', [
			'name' => 'name',
			'column' => 'worker.type'
		]);
	}

	public function test_create__worked_hours_export_column_validation() {
		$this->actingAs($this->admin)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'error',
			'name' => 0
		])->assertRedirect()
			->assertSessionHasErrors([
				'name',
				'column'
			]);
	}
}
