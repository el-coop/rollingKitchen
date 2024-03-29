<?php

namespace Tests\Feature\Admin\BandMember;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\BandMember\UserCreated;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
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

	public function test_guest_cant_create_band_member() {
		$this->get(action('Admin\BandMemberController@create', $this->band->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_band_member() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_worker_cant_create_band_member() {
		$this->actingAs($this->worker)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_accountant_cant_create_band_member() {
		$this->actingAs($this->accountant)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_band_cant_create_band_member() {
		$this->actingAs($this->band)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_band_member_cant_create_band_member() {
		$this->actingAs($this->bandMember)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_artist_manager_cant_create_band_member() {
		$this->actingAs($this->artistManager)->get(action('Admin\BandMemberController@create', $this->band->user))->assertForbidden();
	}

	public function test_admin_cant_create_band_member() {
		$this->actingAs($this->admin)->get(action('Admin\BandMemberController@create', $this->band->user))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'value' => '',
				'label' => __('global.name')
			]);
	}

	public function test_guest_cant_store_band_member() {
		$this->post(action('Admin\BandMemberController@store', $this->band->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_store_band_member() {
		$this->actingAs($this->kitchen)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_worker_cant_store_band_member() {
		$this->actingAs($this->worker)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_accountant_cant_store_band_member() {
		$this->actingAs($this->accountant)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_band_cant_store_band_member() {
		$this->actingAs($this->band)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_band_member_cant_store_band_member() {
		$this->actingAs($this->bandMember)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_artist_manager_cant_store_band_member() {
		$this->actingAs($this->artistManager)->post(action('Admin\BandMemberController@store', $this->band->user))->assertForbidden();
	}

	public function test_admin_can_store_band_member() {
		Notification::fake();

		$this->actingAs($this->admin)->post(action('Admin\BandMemberController@store', $this->band->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'payment' => 0
		])->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'email' => 'a@a.com'
			]);
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'user_type' => BandMember::class
		]);

		$bandMember = User::where(['email' => 'a@a.com', 'user_type' => BandMember::class])->first()->user;
		Notification::assertSentTo($bandMember->user, UserCreated::class);
        $this->assertDatabaseHas('band_members', [
            'id' => $bandMember->id
        ]);
	}

	public function test_admin_cant_go_over_budget() {
		$this->actingAs($this->admin)->post(action('Admin\BandMemberController@store', $this->band->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'payment' => 10
		])->assertRedirect()->assertSessionHasErrors('payment');
	}
}
