<?php

namespace Tests\Feature\Admin\Stages;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Kitchen;
use App\Models\Stage;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrudTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	private $stage;

	protected function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->artistManager = User::factory()->make();
		ArtistManager::factory()->create()->user()->save($this->artistManager);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);

		$this->stage = Stage::factory()->create();
	}

	public function test_guest_cant_see_create_stage_form() {
		$this->get(action('Admin\StageController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_create_stage_form() {
		$this->actingAs($this->worker)->get(action('Admin\StageController@create'))->assertForbidden();
	}

	public function test_kitchen_cant_see_create_stage_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\StageController@create'))->assertForbidden();
	}

	public function test_artist_manager_cant_see_create_stage_form() {
		$this->actingAs($this->artistManager)->get(action('Admin\StageController@create'))->assertForbidden();
	}

	public function test_accountant_cant_see_create_stage_form() {
		$this->actingAs($this->accountant)->get(action('Admin\StageController@create'))->assertForbidden();
	}

	public function test_admin_can_see_create_stage_form() {
		$this->actingAs($this->admin)->get(action('Admin\StageController@create'))->assertSuccessful()->assertJsonFragment([
			'name' => 'name',
			'type' => 'text'
		]);
	}


	public function test_guest_cant_create_stage() {
		$this->post(action('Admin\StageController@store'), ['name' => 'name'])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_create_stage() {
		$this->actingAs($this->worker)->post(action('Admin\StageController@store'), ['name' => 'name'])->assertForbidden();
	}

	public function test_kitchen_cant_create_stage() {
		$this->actingAs($this->kitchen)->post(action('Admin\StageController@store'), ['name' => 'name'])->assertForbidden();
	}

	public function test_artist_manager_cant_create_stage() {
		$this->actingAs($this->artistManager)->post(action('Admin\StageController@store'), ['name' => 'name'])->assertForbidden();
	}

	public function test_accountant_cant_create_stage() {
		$this->actingAs($this->accountant)->post(action('Admin\StageController@store'), ['name' => 'name'])->assertForbidden();
	}

	public function test_admin_can_create_stage() {
		$this->actingAs($this->admin)->post(action('Admin\StageController@store'), ['name' => 'name'])->assertSuccessful()->assertJsonFragment([
			'name' => 'name',
		]);

		$this->assertDatabaseHas('stages', [
			'name' => 'name',
		]);
	}

	public function test_create_stage_validation() {
		$this->actingAs($this->admin)->post(action('Admin\StageController@store'), ['name' => ''])->assertRedirect()->assertSessionHasErrors('name');
	}

	public function test_guest_cant_see_edit_stage_form() {
		$this->get(action('Admin\StageController@edit', $this->stage))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_edit_stage_form() {
		$this->actingAs($this->worker)->get(action('Admin\StageController@edit', $this->stage))->assertForbidden();
	}

	public function test_kitchen_cant_see_edit_stage_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\StageController@edit', $this->stage))->assertForbidden();
	}

	public function test_artist_manager_cant_see_edit_stage_form() {
		$this->actingAs($this->artistManager)->get(action('Admin\StageController@edit', $this->stage))->assertForbidden();
	}

	public function test_accountant_cant_see_edit_stage_form() {
		$this->actingAs($this->accountant)->get(action('Admin\StageController@edit', $this->stage))->assertForbidden();
	}

	public function test_admin_can_see_edit_stage_form() {
		$this->actingAs($this->admin)->get(action('Admin\StageController@edit', $this->stage))->assertSuccessful()->assertJsonFragment([
			'name' => 'name',
			'type' => 'text',
			'value' => $this->stage->name
		]);
	}

	public function test_guest_cant_update_stage() {
		$this->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_update_stage() {
		$this->actingAs($this->worker)->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertForbidden();
	}

	public function test_kitchen_cant_updatestage() {
		$this->actingAs($this->kitchen)->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertForbidden();
	}

	public function test_artist_manager_cant_update_stage() {
		$this->actingAs($this->artistManager)->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertForbidden();
	}

	public function test_accountant_cant_update_stage() {
		$this->actingAs($this->accountant)->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertForbidden();
	}

	public function test_admin_can_update_stage() {
		$this->actingAs($this->admin)->patch(action('Admin\StageController@update', $this->stage), ['name' => 'name'])->assertSuccessful()->assertJsonFragment([
			'name' => 'name',
			'id' => $this->stage->id
		]);

		$this->assertDatabaseHas('stages', [
			'name' => 'name',
			'id' => $this->stage->id
		]);
	}

	public function test_create_update_validation() {
		$this->actingAs($this->admin)->patch(action('Admin\StageController@update', $this->stage), ['name' => ''])->assertRedirect()->assertSessionHasErrors('name');
	}

	public function test_guest_cant_delete_stage() {
		$this->delete(action('Admin\StageController@destroy', $this->stage))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_delete_stage() {
		$this->actingAs($this->worker)->delete(action('Admin\StageController@destroy', $this->stage))->assertForbidden();
	}

	public function test_kitchen_cant_delete_stage() {
		$this->actingAs($this->kitchen)->delete(action('Admin\StageController@destroy', $this->stage))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_stage() {
		$this->actingAs($this->artistManager)->delete(action('Admin\StageController@destroy', $this->stage))->assertForbidden();
	}

	public function test_accountant_cant_delete_stage() {
		$this->actingAs($this->accountant)->delete(action('Admin\StageController@destroy', $this->stage))->assertForbidden();
	}

	public function test_admin_can_delete_stage() {
		$this->actingAs($this->admin)->delete(action('Admin\StageController@destroy', $this->stage))->assertSuccessful()->assertJson([
			'success' => 'true',
		]);

		$this->assertDatabaseMissing('stages', [
			'id' => $this->stage->id,
		]);
	}
}
