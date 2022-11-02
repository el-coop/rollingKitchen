<?php

namespace Tests\Feature\Admin\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
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
		$this->secondBand = User::factory()->make();
		Band::factory()->create()->user()->save($this->secondBand);
	}

	public function test_guest_cant_delete_band() {
		$this->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_band() {
		$this->actingAs($this->kitchen)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertForbidden();
	}

	public function test_worker_cant_delete_band() {
		$this->actingAs($this->worker)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertForbidden();
	}

	public function test_accountant_cant_delete_band() {
		$this->actingAs($this->accountant)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertForbidden();
	}

	public function test_band_cant_delete_band() {
		$this->actingAs($this->band)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_band_from_admin_controller() {
		$this->actingAs($this->artistManager)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertForbidden();
	}

	public function test_admin_cant_delete_band() {
		$this->actingAs($this->admin)->delete(action('Admin\BandController@destroy', $this->secondBand->user))->assertSuccessful();
		$this->assertDatabaseMissing('users', [
			'id' => $this->secondBand->id
		]);

		$this->assertDatabaseMissing('bands', [
			'id' => $this->secondBand->user->id
		]);
	}
}
