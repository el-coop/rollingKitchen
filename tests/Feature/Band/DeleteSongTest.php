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

class DeleteSongTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $secondBand;
	protected $song;
	
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
		
		$this->song = factory(BandSong::class)->create([
			'band_id' => $this->band->user->id
		]);
	}
	
	public function test_guest_cant_update_song_to_band() {
		$this->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_update_song_to_band() {
		$this->actingAs($this->kitchen)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_worker_cant_update_song_to_band() {
		$this->actingAs($this->worker)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_artist_manager_cant_update_song_to_band() {
		$this->actingAs($this->artistManager)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_accountant_cant_update_song_to_band() {
		$this->actingAs($this->accountant)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_band_member_cant_update_song_to_band() {
		$this->actingAs($this->bandMember)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_secondBand_cant_update_song_to_band() {
		$this->actingAs($this->secondBand)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertForbidden();
	}
	
	public function test_band_can_update_song_to_band() {
		$this->actingAs($this->band)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);
		
		$this->assertDatabaseMissing('band_songs', [
			'id' => $this->song->id,
		]);
	}
	
	public function test_admin_can_update_song_to_band() {
		$this->actingAs($this->admin)->delete(action('Band\SongController@update', [
			'band' => $this->band->user,
			'song' => $this->song
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);
		
		$this->assertDatabaseMissing('band_songs', [
			'id' => $this->song->id,
		]);
	}
	
}
