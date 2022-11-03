<?php

namespace Admin\Kitchens\ArtistManager;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
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
	protected $secondArtistManager;

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
		$this->secondArtistManager = User::factory()->make();
		ArtistManager::factory()->create()->user()->save($this->secondArtistManager);

	}

	public function test_guest_cant_delete_artist_manager() {
		$this->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_artist_manager() {
		$this->actingAs($this->kitchen)->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertForbidden();
	}

	public function test_worker_cant_delete_artist_manager() {
		$this->actingAs($this->worker)->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertForbidden();
	}

	public function test_accountant_cant_delete_artist_manager() {
		$this->actingAs($this->accountant)->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_artist_manager() {
		$this->actingAs($this->artistManager)->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertForbidden();
	}

	public function test_admin_can_delete_artist_manager() {
		$this->actingAs($this->admin)->delete(action('Admin\ArtistManagerController@destroy', $this->secondArtistManager->user))->assertSuccessful();
		$this->assertDatabaseMissing('artist_managers', [
			'id' => $this->secondArtistManager->user->id
		]);
		$this->assertDatabaseMissing('users', [
			'id' => $this->secondArtistManager->id
		]);
	}
}
