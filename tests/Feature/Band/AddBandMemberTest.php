<?php

namespace Tests\Feature\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use Notification;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\BandMember\UserCreated;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddBandMemberTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $secondBand;

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
		factory(Band::class)->create()->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->secondBand = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->secondBand);
	}

	public function test_guest_cant_add_band_member(){
		$this->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_add_band_member(){
		$this->actingAs($this->kitchen)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_admin_cant_add_band_member(){
		$this->actingAs($this->admin)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_worker_cant_add_band_member(){
		$this->actingAs($this->worker)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_band_member_cant_add_band_member(){
		$this->actingAs($this->bandMember)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_accountant_cant_add_band_member(){
		$this->actingAs($this->accountant)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_artist_manager_cant_add_band_member(){
		$this->actingAs($this->artistManager)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_second_band_cant_add_band_member(){
		$this->actingAs($this->secondBand)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_band_can_add_band_member_self(){
		Notification::fake();
		$this->actingAs($this->band)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email@mail.com',
			'language' => 'en',
		])->assertSuccessful();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'email@mail.com',
			'user_type' => BandMember::class
		]);
		$this->assertDatabaseHas('band_members', [
			'band_id' => $this->band->user->id,
			'id' => 2
		]);
		$bandMember = User::where(['email' => 'email@mail.com', 'user_type' => BandMember::class])->first()->user;
		Notification::assertSentTo($bandMember->user, UserCreated::class);
	}
}
