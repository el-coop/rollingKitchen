<?php

namespace Admin\Kitchens\KitchenExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;

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
		$this->band = User::factory()->make();
		Band::factory()->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_create_kitchen_column() {
		$this->post(action('Admin\KitchenExportColumnController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_kitchen_column() {
		$this->actingAs($this->kitchen)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_worker_cant_create_kitchen_column() {
		$this->actingAs($this->worker)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_band_cant_create_kitchen_column() {
		$this->actingAs($this->band)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_accountant_cant_create_kitchen_column() {
		$this->actingAs($this->accountant)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_band_member_cant_create_kitchen_column() {
		$this->actingAs($this->bandMember)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_artist_manager_cant_create_kitchen_column() {
		$this->actingAs($this->artistManager)->post(action('Admin\KitchenExportColumnController@create'))->assertForbidden();
	}

	public function test_admin_can_create_kitchen_column() {
		$this->actingAs($this->admin)->post(action('Admin\KitchenExportColumnController@create'), [
			'column' => 'user.name',
		])->assertSuccessful();

		$this->assertDatabaseHas('kitchen_export_columns', [
			'column' => 'user.name',
		]);
	}

	public function test_create_kitchen_column_validation() {
		$this->actingAs($this->admin)->post(action('Admin\KitchenExportColumnController@create'), [
			'column' => 'name',
		])->assertSessionHasErrors(['column']);
	}
}
