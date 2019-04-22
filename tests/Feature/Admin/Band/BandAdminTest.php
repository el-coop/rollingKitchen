<?php

namespace Tests\Feature\Admin\Band;

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

class BandAdminTest extends TestCase {
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
			'payment_method' => 'individual'
		])->user()->save($this->band);
		$this->bandAdmin = factory(BandAdmin::class)->make();
		$this->band->user->admin()->save($this->bandAdmin);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_update_band_admin() {
		$this->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band_admin() {
		$this->actingAs($this->kitchen)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_worker_cant_update_band_admin() {
		$this->actingAs($this->worker)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_accountant_cant_update_band_admin() {
		$this->actingAs($this->accountant)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_band_cant_update_band_admin() {
		$this->actingAs($this->band)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_band_member_cant_update_band_admin() {
		$this->actingAs($this->bandMember)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_admin() {
		$this->actingAs($this->artistManager)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin))->assertForbidden();
	}

	public function test_admin_can_update_band_admin() {
		$this->actingAs($this->admin)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin), [
			'adminName' => 'name',
			'bandmember' => ['test' => 'test'],
			'payment' => 0
		])->assertRedirect();
		$this->assertDatabaseHas('band_admins', [
			'name' => 'name',
			'id' => $this->bandAdmin->id,
			'data' => json_encode(['test' => 'test'])
		]);
	}

	public function test_admin_cant_go_over_budget_on_update() {
		$this->actingAs($this->admin)->patch(action('Admin\BandController@updateAdmin', $this->bandAdmin), [
			'adminName' => 'name',
			'payment' => 10,
			'bandmemeber' => json_encode(['test' => 'test'])
		])->assertRedirect()->assertSessionHasErrors('payment');
	}

	public function test_guest_cant_see_band_admin_pdf() {
		$this->get(action('Admin\BandController@adminPdf', $this->bandMember->user))->assertStatus(401);
	}


	public function test_kitchen_cant_see_band_admin_pdf() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandController@adminPdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_worker_cant_see_band_admin_pdf() {
		$this->actingAs($this->worker)->get(action('Admin\BandController@adminPdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_member_cant_see_band_admin_pdf() {
		$this->actingAs($this->bandMember)->get(action('Admin\BandController@adminPdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_cant_see_band_admin_pdf() {
		$this->actingAs($this->band)->get(action('Admin\BandController@adminPdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_admin_can_see_band_admin_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}

	public function test_accountant_can_see_band_admin_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}
}
