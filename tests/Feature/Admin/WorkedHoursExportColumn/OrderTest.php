<?php

namespace Tests\Feature\Admin\WorkedHoursExportColumn;

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
	protected $worker;
	protected $workedHoursColumns;

	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$i = 1;
		$this->workedHoursColumns = factory(WorkedHoursExportColumn::class,4)->make()->each(function ($workedHoursColumn) use ($i) {
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
