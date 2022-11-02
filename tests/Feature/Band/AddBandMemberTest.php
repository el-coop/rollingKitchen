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
		Band::factory()->create()->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->secondBand = User::factory()->make();
		Band::factory()->create()->user()->save($this->secondBand);
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
			'payment' => 0
		])->assertSuccessful();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'email@mail.com',
			'user_type' => BandMember::class
		]);

		$bandMember = User::where(['email' => 'email@mail.com', 'user_type' => BandMember::class])->first();
		Notification::assertSentTo($bandMember, UserCreated::class);
        $this->assertDatabaseHas('band_members', [
            'band_id' => $this->band->user->id,
            'id' => $bandMember->user->id
        ]);
	}

	public function test_band_cant_create_band_member_over_budget(){
		$this->actingAs($this->band)->post(action('Band\BandController@addBandMember', $this->band->user), [
			'name' => 'name',
			'email' => 'email@mail.com',
			'language' => 'en',
			'payment' => 10
		])->assertRedirect()->assertSessionHasErrors('payment');
	}
}
