<?php

namespace Tests\Feature\Admin\WorkedHoursExportColumn;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use App\Services\WorkedHoursService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $workedHoursColumn;

	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->workedHoursColumn = factory(WorkedHoursExportColumn::class)->create([
			'column' => 'worker.type',
			'order' => 0
		]);
	}

	public function test_guest_cant_update_worked_hours_export_column() {
		$this->patch(action('Admin\WorkedHoursExportColumnController@update', $this->workedHoursColumn), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_worked_hours_export_column() {
		$this->actingAs($this->kitchen)->patch(action('Admin\WorkedHoursExportColumnController@update', $this->workedHoursColumn), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertForbidden();
	}

	public function test_worker_cant_update_worked_hours_export_column() {
		$this->actingAs($this->worker)->patch(action('Admin\WorkedHoursExportColumnController@update', $this->workedHoursColumn), [
			'column' => 'worker.type',
			'name' => 'name'
		])->assertForbidden();
	}

	public function test_admin_can_update_worked_hours_export_column() {
		$this->actingAs($this->admin)->patch(action('Admin\WorkedHoursExportColumnController@update', $this->workedHoursColumn), [
			'column' => 'user.email',
			'name' => 'name'
		])->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'column' => 'user.email',
				'id' => $this->workedHoursColumn->id
			]);
		$this->assertDatabaseHas('worked_hours_export_columns', [
			'name' => 'name',
			'column' => 'user.email',
			'id' => $this->workedHoursColumn->id
		]);
	}

	public function test_update__worked_hours_export_column_validation() {
		$this->actingAs($this->admin)->patch(action('Admin\WorkedHoursExportColumnController@update', $this->workedHoursColumn), [
			'column' => 'error',
			'name' => 0
		])->assertRedirect()
			->assertSessionHasErrors([
				'name',
				'column'
			]);
	}
}
