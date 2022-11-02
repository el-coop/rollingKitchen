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

class OrderTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $worker;
	protected $workedHoursColumns;

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
		$i = 1;
		$this->workedHoursColumns = WorkedHoursExportColumn::factory(4)->make()->each(function ($workedHoursColumn) use ($i) {
			$workedHoursColumn->column = 'user.email';
			$i = $i + 1;
			$workedHoursColumn->order = $i;
			$workedHoursColumn->save();
		});
	}

	public function test_guest_cant_order_worked_hours_export_column() {
		$this->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => $this->workedHoursColumns->pluck('id')
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_order_worked_hours_export_column() {
		$this->actingAs($this->kitchen)->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => $this->workedHoursColumns->pluck('id')
		])->assertForbidden();
	}

	public function test_accountant_cant_order_worked_hours_export_column() {
		$this->actingAs($this->accountant)->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => $this->workedHoursColumns->pluck('id')
		])->assertForbidden();
	}

	public function test_worker_cant_order_worked_hours_export_column() {
		$this->actingAs($this->worker)->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => $this->workedHoursColumns->pluck('id')
		])->assertForbidden();
	}

	public function test_admin_can_order_worked_hours_export_column() {
		$newOrder = $this->workedHoursColumns->pluck('id')->shuffle()->toArray();
		$this->actingAs($this->admin)->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => $newOrder
		])->assertSuccessful();

		$order = WorkedHoursExportColumn::orderBy('order')->get()->pluck('id')->toArray();
		$this->assertEquals($newOrder,$order);

	}

	public function test_order__worked_hours_export_column_validation() {
		$this->actingAs($this->admin)->patch(action('Admin\WorkedHoursExportColumnController@saveOrder'), [
			'order' => 'order'
		])->assertRedirect()
			->assertSessionHasErrors([
				'order'
			]);
	}
}
