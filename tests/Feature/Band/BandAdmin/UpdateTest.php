<?php

namespace Tests\Feature\Band\BandAdmin;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandAdmin;
use App\Models\BandMember;
use App\Models\Kitchen;
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
	protected $bandAdmin;
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
			'payment_method' => 'individual'
		])->user()->save($this->band);
		$this->bandAdmin = BandAdmin::factory()->make();
		$this->band->user->admin()->save($this->bandAdmin);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_update_band_admin() {
		$this->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band_admin() {
		$this->actingAs($this->kitchen)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_worker_cant_update_band_admin() {
		$this->actingAs($this->worker)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_accountant_cant_update_band_admin() {
		$this->actingAs($this->accountant)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_admin_cant_update_band_admin() {
		$this->actingAs($this->admin)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_band_member_cant_update_band_admin() {
		$this->actingAs($this->bandMember)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_admin() {
		$this->actingAs($this->artistManager)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_band_can_update_band_admin() {
		$this->actingAs($this->band)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]), [
			'adminName' => 'name',
			'bandmember' => ['test' => 'test'],
			'payment' => 0
		])->assertRedirect();
		$this->assertDatabaseHas('band_admins', [
			'name' => 'name',
			'id' => $this->bandAdmin->id,
		]);

        $bandAdmin = BandAdmin::find($this->bandAdmin->id);
        $this->assertEquals(collect(['test' => 'test']), $bandAdmin->data);
	}

	public function test_band_cant_go_over_budget_on_update() {
		$this->actingAs($this->band)->patch(action('Band\BandAdminController@update', [$this->band->user, $this->bandAdmin]), [
			'name' => 'name',
			'payment' => 10,
			'bandmemeber' => json_encode(['test' => 'test'])
		])->assertRedirect()->assertSessionHasErrors('payment');
	}
}
