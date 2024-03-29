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
	}

	public function test_guest_cant_create_worked_hours_export_column() {
		$this->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_worked_hours_export_column() {
		$this->actingAs($this->kitchen)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
		])->assertForbidden();
	}

	public function test_worker_cant_create_worked_hours_export_column() {
		$this->actingAs($this->worker)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
		])->assertForbidden();
	}

	public function test_accountant_cant_create_worked_hours_export_column() {
		$this->actingAs($this->accountant)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
		])->assertForbidden();
	}

	public function test_admin_can_create_worked_hours_export_column() {
		$this->actingAs($this->admin)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'worker.type',
		])->assertSuccessful()
			->assertJsonFragment([
				'column' => 'worker.type'
			]);
		$this->assertDatabaseHas('worked_hours_export_columns', [
			'column' => 'worker.type'
		]);
	}

	public function test_create__worked_hours_export_column_validation() {
		$this->actingAs($this->admin)->post(action('Admin\WorkedHoursExportColumnController@create'), [
			'column' => 'error',
		])->assertRedirect()
			->assertSessionHasErrors([
				'column'
			]);
	}
}
