<?php

namespace Tests\Feature\Admin\Band;

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

class UpdateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $secondBand;
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
		Band::factory()->create()->user()->save($this->band);
		$this->secondBand = User::factory()->make();
		Band::factory()->create()->user()->save($this->secondBand);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_get_edit_form() {
		$this->get(action('Admin\BandController@edit', $this->secondBand->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_edit_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_worker_cant_get_edit_form() {
		$this->actingAs($this->worker)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_accountant_cant_get_edit_form() {
		$this->actingAs($this->accountant)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_band_cant_get_edit_form() {
		$this->actingAs($this->band)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_band_member_cant_get_edit_form() {
		$this->actingAs($this->bandMember)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_artist_manager_cant_get_edit_form_from_admin_controller() {
		$this->actingAs($this->artistManager)->get(action('Admin\BandController@edit', $this->secondBand->user))->assertForbidden();
	}

	public function test_admin_can_get_edit_form() {
		$this->actingAs($this->admin)->get(action('Admin\BandController@edit', $this->secondBand->user))
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
		$this->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band() {
		$this->actingAs($this->kitchen)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_worker_cant_update_band() {
		$this->actingAs($this->worker)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_accountant_cant_update_band() {
		$this->actingAs($this->accountant)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_band_cant_update_band() {
		$this->actingAs($this->band)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_from_admin_controller() {
		$this->actingAs($this->artistManager)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_admin_can_update_band() {
		$this->actingAs($this->admin)->patch(action('Admin\BandController@update', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'paymentMethod' => 'individual',
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
			'payment_method' => 'individual'
		]);
		$this->assertDatabaseHas('band_admins', [
			'band_id' => $this->secondBand->user->id
		]);
		$band = Band::find($this->secondBand->user->id);
        $this->assertEquals(collect(['test' => 'test']), $band->data);
    }

	public function test_guest_cant_non_ajax_update_band() {
		$this->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_non_ajax_update_band() {
		$this->actingAs($this->kitchen)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_worker_cant_non_ajax_update_band() {
		$this->actingAs($this->worker)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_accountant_cant_non_ajax_update_band() {
		$this->actingAs($this->accountant)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_band_cant_non_ajax_update_band() {
		$this->actingAs($this->band)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_band_member_cant_non_ajax_update_band() {
		$this->actingAs($this->bandMember)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_artist_manager_cant_non_ajax_update_band_from_admin_controller() {
		$this->actingAs($this->artistManager)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'band' => ['test' => 'test']
		])->assertForbidden();
	}

	public function test_admin_can_non_ajax_update_band() {
		$this->actingAs($this->admin)->patch(action('Admin\BandController@nonAjaxUpdate', $this->secondBand->user), [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'paymentMethod' => 'individual',
			'band' => ['test' => 'test']
		])->assertRedirect();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'test@test.com',
			'language' => 'nl',
			'id' => $this->secondBand->id
		]);
		$this->assertDatabaseHas('bands', [
			'id' => $this->secondBand->user->id,
			'payment_method' => 'individual'
		]);
		$this->assertDatabaseHas('band_admins', [
			'band_id' => $this->secondBand->user->id
		]);
		$band = Band::find($this->secondBand->user->id);
        $this->assertEquals(collect(['test' => 'test']), $band->data);
    }
}
