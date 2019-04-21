<?php

namespace Tests\Feature\Admin\KitchenExportColumn;

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

class DestroyTest extends TestCase {
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
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->artistManager = factory(User::class)->make();
		factory(ArtistManager::class)->create()->user()->save($this->artistManager);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->band = factory(User::class)->make();
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->kitchenColumn = factory(KitchenExportColumn::class)->create([
			'column' => 'user.name',
			'order' => 0
		]);
	}

	public function test_guest_cant_create_kitchen_column() {
		$this->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_kitchen_column() {
		$this->actingAs($this->kitchen)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_worker_cant_create_kitchen_column() {
		$this->actingAs($this->worker)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_band_cant_create_kitchen_column() {
		$this->actingAs($this->band)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_accountant_cant_create_kitchen_column() {
		$this->actingAs($this->accountant)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_band_member_cant_create_kitchen_column() {
		$this->actingAs($this->bandMember)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_artist_manager_cant_create_kitchen_column() {
		$this->actingAs($this->artistManager)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertForbidden();
	}

	public function test_admin_can_create_kitchen_column() {
		$this->actingAs($this->admin)->delete(action('Admin\KitchenExportColumnController@destroy', $this->kitchenColumn))->assertSuccessful();

		$this->assertDatabaseMissing('kitchen_export_columns', [
			'id' => $this->kitchenColumn->id
		]);
	}
}
