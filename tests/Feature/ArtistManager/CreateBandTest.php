<?php

namespace Tests\Feature\ArtistManager;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\Band\UserCreated;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBandTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;

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
	}

	public function test_guest_cant_create_band() {
		$this->get(action('ArtistManager\ArtistManagerController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_band() {
		$this->actingAs($this->kitchen)->get(action('ArtistManager\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_worker_cant_create_band() {
		$this->actingAs($this->worker)->get(action('ArtistManager\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_accountant_cant_create_band() {
		$this->actingAs($this->accountant)->get(action('ArtistManager\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_band_cant_create_band() {
		$this->actingAs($this->band)->get(action('ArtistManager\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_admin_cant_create_band_with_artist_manager_controller() {
		$this->actingAs($this->admin)->get(action('ArtistManager\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_artist_manager_can_create_band() {
		$this->actingAs($this->artistManager)->get(action('ArtistManager\ArtistManagerController@create'))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'value' => ''
			])
			->assertJsonFragment([
				'name' => 'email',
				'value' => ''
			]);
	}

	public function test_guest_cant_store_band() {
		$this->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_store_band() {
		$this->actingAs($this->kitchen)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_worker_cant_store_band() {
		$this->actingAs($this->worker)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_accountant_cant_store_band() {
		$this->actingAs($this->accountant)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_band_cant_store_band() {
		$this->actingAs($this->band)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_admin_cant_store_band_with_artist_manager_controller() {
		$this->actingAs($this->admin)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en'
		])->assertForbidden();
	}

	public function test_artist_manager_can_store_band() {
		Notification::fake();
		$this->actingAs($this->artistManager)->post(action('ArtistManager\ArtistManagerController@store'),[
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en',
			'paymentMethod' => 'band',

		])
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'email' => 'test@test.com'
			]);

		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'en',
			'user_type' => Band::class
		]);
		$newBand = User::where(['email' => 'test@test.com', 'user_type' => Band::class])->first()->user;
		$this->assertDatabaseHas('bands', [
			'id' => $newBand->id,
			'payment_method' => 'band',

		]);
		Notification::assertSentTo($newBand->user, UserCreated::class);
	}
}
