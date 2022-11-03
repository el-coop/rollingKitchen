<?php

namespace Admin\Kitchens\KitchenExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\KitchenExportColumn;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $kitchenColumn;

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
		$this->kitchenColumn = KitchenExportColumn::factory()->create([
			'column' => 'user.name',
			'order' => 0
		]);
	}

	public function test_guest_cant_update_kitchen_column() {
		$this->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_kitchen_column() {
		$this->actingAs($this->kitchen)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_worker_cant_update_kitchen_column() {
		$this->actingAs($this->worker)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_band_cant_update_kitchen_column() {
		$this->actingAs($this->band)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_accountant_cant_update_kitchen_column() {
		$this->actingAs($this->accountant)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_band_member_cant_update_kitchen_column() {
		$this->actingAs($this->bandMember)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_artist_manager_cant_update_kitchen_column() {
		$this->actingAs($this->artistManager)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn))->assertForbidden();
	}

	public function test_admin_can_update_kitchen_column() {
		$this->actingAs($this->admin)->patch(action('Admin\KitchenExportColumnController@update', $this->kitchenColumn), [
			'column' => 'user.email'
		])->assertSuccessful();

		$this->assertDatabaseHas('kitchen_export_columns', [
			'id' => $this->kitchenColumn->id,
			'column' => 'user.email'
		]);
	}
}
