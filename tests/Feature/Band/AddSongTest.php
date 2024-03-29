<?php

namespace Tests\Feature\Band;

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

class AddSongTest extends TestCase {
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

	public function test_guest_cant_add_song_to_band() {
		$this->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_add_song_to_band() {
		$this->actingAs($this->kitchen)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_worker_cant_add_song_to_band() {
		$this->actingAs($this->worker)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_artist_manager_cant_add_song_to_band() {
		$this->actingAs($this->artistManager)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_accountant_cant_add_song_to_band() {
		$this->actingAs($this->accountant)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_band_member_cant_add_song_to_band() {
		$this->actingAs($this->bandMember)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_secondBand_cant_add_song_to_band() {
		$this->actingAs($this->secondBand)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertForbidden();
	}

	public function test_band_can_add_song_to_band() {
		$this->actingAs($this->band)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertSuccessful()->assertJson([
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		]);

		$this->assertDatabaseHas('band_songs', [
			'band_id' => $this->band->user->id,
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		]);
	}


	public function test_admin_can_add_song_to_band() {
		$this->actingAs($this->admin)->post(action('Band\SongController@create', $this->band->user), [
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		])->assertSuccessful()->assertJson([
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		]);

		$this->assertDatabaseHas('band_songs', [
			'band_id' => $this->band->user->id,
			'title' => 'bla',
			'composer' => 'bla',
			'owned' => '0',
			'protected' => '1',
		]);
	}

	public function test_add_song_to_band_validation() {
		$this->actingAs($this->band)->post(action('Band\SongController@create', $this->band->user), [
			'title' => '',
			'composer' => '',
			'owned' => 'asd',
			'protected' => 'asd',
		])->assertRedirect()->assertSessionHasErrors([
			'title', 'composer', 'owned','protected'
		]);
	}
}
