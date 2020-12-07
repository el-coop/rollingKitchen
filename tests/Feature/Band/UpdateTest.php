<?php

namespace Tests\Feature\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandSong;
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
	protected $secondBand;
	
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
		factory(Band::class)->create()->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->secondBand = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->secondBand);
	}
	
	public function test_guest_cant_update_band() {
		$this->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_update_band() {
		$this->actingAs($this->kitchen)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_admin_cant_update_band() {
		$this->actingAs($this->admin)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_worker_cant_update_band() {
		$this->actingAs($this->worker)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_band_member_cant_update_band() {
		$this->actingAs($this->bandMember)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_accountant_cant_update_band() {
		$this->actingAs($this->accountant)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_artist_manager_cant_update_band() {
		$this->actingAs($this->artistManager)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_second_band_cant_update_band() {
		$this->actingAs($this->secondBand)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email',
			'paymentMethod' => 'band',
			'data' => [],
			'language' => 'en'
		])->assertForbidden();
	}
	
	public function test_band_can_update_self() {
		$this->actingAs($this->band)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email@mail.com',
			'paymentMethod' => 'band',
			'band' => ['test' => 'test'],
			'language' => 'en'
		])->assertRedirect();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'email@mail.com',
			'id' => $this->band->id
		]);
		$this->assertDatabaseHas('bands', [
			'payment_method' => 'band',
			'id' => $this->band->user->id
		]);
        $band = Band::find($this->band->user->id);
        $this->assertEquals(collect(['test'=>'test']), $band->data);
	}
	
	public function test_band_cant_submit_review_without_tracks() {
		$this->actingAs($this->band)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email@mail.com',
			'paymentMethod' => 'band',
			'band' => ['test' => 'test'],
			'language' => 'en',
			'review' => true
		])->assertRedirect()->assertSessionHasErrors('tracks');
	}
	
	public function test_band_can_submit_review_with_tracks() {
		$this->band->user->bandSongs()->save(factory(BandSong::class)->make());
		$this->actingAs($this->band)->patch(action('Band\BandController@update', $this->band->user), [
			'name' => 'name',
			'email' => 'email@mail.com',
			'paymentMethod' => 'band',
			'band' => ['test' => 'test'],
			'language' => 'en',
			'review' => true
		])->assertRedirect();
		
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'email@mail.com',
			'id' => $this->band->id
		]);
		$this->assertDatabaseHas('bands', [
			'payment_method' => 'band',
			'id' => $this->band->user->id,
			'submitted' => true
		]);
		$band = Band::find($this->band->user->id);
		$this->assertEquals(collect(['test'=>'test']), $band->data);
	}
	
	public function test_band_update_validation() {
		$this->actingAs($this->band)->patch(action('Band\BandController@update', $this->band->user), [
			'email' => 'bla',
			'name' => 'g',
			'language' => '',
			'band' => 'test',
			'paymentMethod' => 'yay'
		])->assertSessionHasErrors([
			'email', 'name', 'language', 'band', 'paymentMethod'
		]);
		
	}
	
}
