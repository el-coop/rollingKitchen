<?php

namespace Tests\Feature\ArtistManager;

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

class UpdateBandTest extends TestCase {
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
		$this->secondBand = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->secondBand);
	}

	public function test_guest_cant_get_edit_form() {
		$this->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_edit_form() {
		$this->actingAs($this->kitchen)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_worker_cant_get_edit_form() {
		$this->actingAs($this->worker)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_accountant_cant_get_edit_form() {
		$this->actingAs($this->accountant)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_band_cant_get_edit_form() {
		$this->actingAs($this->band)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_admin_cant_get_edit_form_from_artist_manager_controller() {
		$this->actingAs($this->admin)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_artist_manager_can_get_edit_form() {
		$this->actingAs($this->artistManager)->get(action('ArtistManager\ArtistManagerController@edit', $this->secondBand->user))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'value' => $this->secondBand->name
			])
			->assertJsonFragment([
				'name' => 'email',
				'value' => $this->secondBand->email
			])
			->assertJsonFragment([
				'name' => 'language',
				'value' => $this->secondBand->language
			]);
	}

	public function test_guest_cant_update_band() {
		$this->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band() {
		$this->actingAs($this->kitchen)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_worker_cant_update_band() {
		$this->actingAs($this->worker)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_accountant_cant_update_band() {
		$this->actingAs($this->accountant)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_band_cant_update_band() {
		$this->actingAs($this->band)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_admin_cant_update_band_from_artist_manager_controller() {
		$this->actingAs($this->admin)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_artist_manager_can_update_band() {
		$this->actingAs($this->artistManager)->patch(action('ArtistManager\ArtistManagerController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'paymentMethod' => 'band',
			'band' => ['test' => 'test']
		])->assertSuccessful()
			->assertJsonFragment([
				'id' => $this->secondBand->user->id,
				'name' => 'name',
				'email' => 'test@test.com'
			]);

		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'id' => $this->secondBand->id
		]);
		$this->assertDatabaseHas('bands', [
			'id' => $this->secondBand->user->id,
			'payment_method' => 'band'
		]);
		$band = Band::find($this->secondBand->user->id);
		$this->assertEquals(collect(['test' => 'test']), $band->data);
	}
}
