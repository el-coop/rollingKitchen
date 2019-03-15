<?php

namespace Tests\Feature\BandMember;

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

	protected function setUp() {
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
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_update_band_member(){
		$this->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band_member(){
		$this->actingAs($this->kitchen)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_worker_cant_update_band_member(){
		$this->actingAs($this->worker)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_accountant_cant_update_band_member(){
		$this->actingAs($this->accountant)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_band_cant_update_band_member(){
		$this->actingAs($this->band)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_band_admin_cant_update_band_member(){
		$this->actingAs($this->admin)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_member(){
		$this->actingAs($this->artistManager)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user))->assertForbidden();
	}

	public function test_band_member_can_update_band_member(){
		$this->actingAs($this->bandMember)->patch(action('BandMember\BandMemberController@update',$this->bandMember->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test']
		])
			->assertRedirect();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'user_type' => BandMember::class,
			'id' => $this->bandMember->id
		]);
		$this->assertDatabaseHas('band_members', [
			'id' => $this->bandMember->user->id,
			'data' => json_encode(['test' => 'test'])
		]);
	}
}
