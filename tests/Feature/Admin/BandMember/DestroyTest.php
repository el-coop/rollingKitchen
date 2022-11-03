<?php

namespace Admin\Kitchens\BandMember;

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

class DestroyTest extends TestCase {
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
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_delete_band_member() {
		$this->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_band_member() {
		$this->actingAs($this->kitchen)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_worker_cant_delete_band_member() {
		$this->actingAs($this->worker)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_accountant_cant_delete_band_member() {
		$this->actingAs($this->accountant)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_cant_delete_band_member() {
		$this->actingAs($this->band)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_band_member_cant_delete_band_member() {
		$this->actingAs($this->bandMember)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_band_member() {
		$this->actingAs($this->artistManager)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))->assertForbidden();
	}

	public function test_admin_cant_delete_band_member() {
		$this->actingAs($this->admin)->delete(action('Admin\BandMemberController@destroy', [$this->band->user, $this->bandMember->user]))
			->assertSuccessful();
		$this->assertDatabaseMissing('users', [
			'id' => $this->bandMember->id
		]);
		$this->assertDatabaseMissing('band_members', [
			'id' => $this->bandMember->user->id
		]);
	}
}
