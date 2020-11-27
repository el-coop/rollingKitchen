<?php

namespace Tests\Feature\Admin\BandMember;
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

class UpdateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
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
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_get_update_band_member_from(){
		$this->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_update_band_member_from(){
		$this->actingAs($this->kitchen)->get(action('Admin\BandMemberController@edit',[$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_worker_cant_get_update_band_member_from(){
		$this->actingAs($this->worker)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_accountant_cant_get_update_band_member_from(){
		$this->actingAs($this->accountant)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_cant_get_update_band_member_from(){
		$this->actingAs($this->band)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_member_cant_get_update_band_member_from(){
		$this->actingAs($this->bandMember)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_artist_manager_cant_get_update_band_member_from(){
		$this->actingAs($this->artistManager)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_admin_can_get_update_band_member_from(){
		$this->actingAs($this->admin)->get(action('Admin\BandMemberController@edit', [$this->band->user, $this->bandMember->user]))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'value' => $this->bandMember->name,
				'label' => __('global.name')
			]);
	}

	public function test_guest_cant_update_band_member(){
		$this->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band_member(){
		$this->actingAs($this->kitchen)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_worker_cant_update_band_member(){
		$this->actingAs($this->worker)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_accountant_cant_update_band_member(){
		$this->actingAs($this->accountant)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_cant_update_band_member(){
		$this->actingAs($this->band)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_member_cant_update_band_member(){
		$this->actingAs($this->bandMember)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_member(){
		$this->actingAs($this->artistManager)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_admin_can_update_band_member(){
		$this->actingAs($this->admin)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test'],
			'payment' => 0
		])->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'email' => 'a@a.com'
			]);
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'user_type' => BandMember::class,
			'id' => $this->bandMember->id
		]);
		$this->assertDatabaseHas('band_members', [
			'id' => $this->bandMember->user->id,
			'data' => json_encode(['test' => 'test']),
		]);
	}
	
	public function test_admin_cant_go_over_budget_on_update() {
		$this->actingAs($this->admin)->patch(action('Admin\BandMemberController@update', [$this->band->user, $this->bandMember->user]), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'payment' => 10
		])->assertRedirect()->assertSessionHasErrors('payment');
	}
}
