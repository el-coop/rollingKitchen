<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\WorkFunction;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkFunctionTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $workplace;
	protected $workFunction;

	protected function setUp() {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->workplace = factory(Workplace::class)->create()->each(function ($workplace) {
			$this->workFunction = factory(WorkFunction::class)->make();
			$workplace->workFunctions()->save($this->workFunction);
		});
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function test_guest_cant_create_workFunction() {
		$this->post(action('Admin\WorkplaceController@addWorkFunction', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_workFunction() {
		$this->actingAs($this->kitchen->user)->post(action('Admin\WorkplaceController@addWorkFunction', $this->workplace))->assertForbidden();
	}

	public function test_admin_can_create_workFunction() {
		$response = $this->actingAs($this->admin->user)->post(action('Admin\WorkplaceController@addWorkFunction',
			$this->workplace), [
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
		])->assertSuccessful();
		$response->assertJsonFragment([
			'name' => 'name',
			'payment_per_hour_after_tax' => 10,
			'payment_per_hour_before_tax' => 12,
			'workplace_id' => 1
		]);

		$this->assertDatabaseHas('work_functions', [
			'name' => 'name',
			'payment_per_hour_before_tax' => 12,
			'payment_per_hour_after_tax' => 10,
			'workplace_id' => 1
		]);
	}

	public function test_guest_cant_delete_workFunction() {
		$this->delete(action('Admin\WorkplaceController@destroyWorkFunction', [
			$this->workplace, $this->workFunction
		]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_workFunction() {
		$this->actingAs($this->kitchen->user)->delete(action('Admin\WorkplaceController@destroyWorkFunction', [$this->workplace, $this->workFunction]))->assertForbidden();
	}

	public function test_admin_can_delete_workFunction() {
		$this->actingAs($this->admin->user)->delete(action('Admin\WorkplaceController@destroyWorkFunction', [
			$this->workplace,
			$this->workFunction
		]))->assertSuccessful()->assertJsonFragment(['success' => true]);
		$this->assertDatabaseMissing('work_functions', [
			'id' => $this->workFunction->id
		]);
	}

	public function test_guest_cant_update_workFunction() {
		$this->patch(action('Admin\WorkplaceController@updateWorkFunction', [
			$this->workplace, $this->workFunction
		]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_workFunction() {
		$this->actingAs($this->kitchen->user)->patch(action('Admin\WorkplaceController@updateWorkFunction', [$this->workplace, $this->workFunction]))->assertForbidden();
	}

	public function test_admin_can_update_workFunction() {
		$response = $this->actingAs($this->admin->user)->patch(action('Admin\WorkplaceController@updateWorkFunction', [
			$this->workplace,
			$this->workFunction,
		]), [
			'name' => 'new name',
			'payment_per_hour_after_tax' => 15,
			'payment_per_hour_before_tax' => 20
		])->assertSuccessful();
		$response->assertJsonFragment([
			'id' => $this->workFunction->id,
			'name' => 'new name',
			'payment_per_hour_after_tax' => 15,
			'payment_per_hour_before_tax' => 20,
		]);
		$this->assertDatabaseHas('work_functions', [
			'id' => $this->workFunction->id,
			'name' => 'new name',
			'payment_per_hour_after_tax' => 15,
			'payment_per_hour_before_tax' => 20,
			'workplace_id' => $this->workFunction->workplace->id

		]);
	}


}
