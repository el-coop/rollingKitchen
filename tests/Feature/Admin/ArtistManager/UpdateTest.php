<?php

namespace Tests\Feature\Admin\ArtistManager;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
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

	public function test_guest_cant_see_update_artist_manager_form(){
		$this->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_update_artist_manager_form(){
		$this->actingAs($this->kitchen)->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertForbidden();
	}

	public function test_worker_cant_see_update_artist_manager_form(){
		$this->actingAs($this->worker)->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertForbidden();
	}

	public function test_accountant_cant_see_update_artist_manager_form(){
		$this->actingAs($this->accountant)->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertForbidden();
	}

	public function test_artist_manager_cant_see_update_artist_manager_form(){
		$this->actingAs($this->artistManager)->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertForbidden();
	}

	public function test_admin_can_see_update_artist_manager_form(){
		$this->actingAs($this->admin)->get(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user))->assertSuccessful();
	}

	public function test_guest_cant_update_artist_manager(){
		$this->patch(action('Admin\ArtistManagerController@update',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_artist_manager(){
		$this->actingAs($this->kitchen)->patch(action('Admin\ArtistManagerController@update',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_worker_cant_update_artist_manager(){
		$this->actingAs($this->worker)->patch(action('Admin\ArtistManagerController@update',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_accountant_cant_update_artist_manager(){
		$this->actingAs($this->accountant)->patch(action('Admin\ArtistManagerController@update',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_artist_manager_cant_update_artist_manager(){
		$this->actingAs($this->artistManager)->patch(action('Admin\ArtistManagerController@update',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_admin_can_update_artist_manager(){
		$this->actingAs($this->admin)->patch(action('Admin\ArtistManagerController@edit',$this->secondArtistManager->user), [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertSuccessful()
		->assertJsonFragment([
			'id' => $this->secondArtistManager->user->id,
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en'
		]);
		$this->assertDatabaseHas('users', [
			'name' => 'new name',
			'email' => 'test@test.com',
			'language' => 'en',
			'user_type' => ArtistManager::class,
			'id' => $this->secondArtistManager->id
		]);
	}

}
