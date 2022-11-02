<?php

namespace Tests\Feature\Admin\WorkedHoursExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $worker;
	protected $workedHoursColumn;

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
		$this->workedHoursColumn = WorkedHoursExportColumn::factory()->create([
			'column' => 'worker.type',
			'order' => 0
		]);
	}

	public function test_guest_cant_destroy_worked_hours_export_column(){
		$this->delete(action('Admin\WorkedHoursExportColumnController@destroy', $this->workedHoursColumn))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_destroy_worked_hours_export_column(){
		$this->actingAs($this->kitchen)->delete(action('Admin\WorkedHoursExportColumnController@destroy', $this->workedHoursColumn))->assertForbidden();
	}

	public function test_worker_cant_destroy_worked_hours_export_column(){
		$this->actingAs($this->worker)->delete(action('Admin\WorkedHoursExportColumnController@destroy', $this->workedHoursColumn))->assertForbidden();
	}

	public function test_accountant_cant_destroy_worked_hours_export_column(){
		$this->actingAs($this->accountant)->delete(action('Admin\WorkedHoursExportColumnController@destroy', $this->workedHoursColumn))->assertForbidden();
	}

	public function test_admin_can_destroy_worked_hours_export_column(){
		$this->actingAs($this->admin)->delete(action('Admin\WorkedHoursExportColumnController@destroy', $this->workedHoursColumn))->assertSuccessful();
		$this->assertDatabaseMissing('worked_hours_export_columns', [
			'id' => $this->workedHoursColumn->id
		]);
	}
}
