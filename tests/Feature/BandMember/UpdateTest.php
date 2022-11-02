<?php

namespace Tests\Feature\BandMember;

use App\Events\BandMember\BandMemberProfileFilled;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandMemberPhoto;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\BandMember\ProfileFilledNotification;
use Event;
use Notification;
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

	public function test_guest_cant_update_band_member() {
		$this->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_update_band_member() {
		$this->actingAs($this->kitchen)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_worker_cant_update_band_member() {
		$this->actingAs($this->worker)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_accountant_cant_update_band_member() {
		$this->actingAs($this->accountant)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_cant_update_band_member() {
		$this->actingAs($this->band)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_admin_cant_update_band_member() {
		$this->actingAs($this->admin)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_artist_manager_cant_update_band_member() {
		$this->actingAs($this->artistManager)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user))->assertForbidden();
	}

	public function test_other_band_member_cant_update_band_member() {
		$bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($bandMember);

		$this->actingAs($bandMember)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test'],
		])->assertForbidden();

	}

	public function test_band_member_cant_review_without_photo() {
		$this->actingAs($this->bandMember)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test'],
			'review' => true
		])->assertRedirect()->assertSessionHasErrors('photos');

	}

	public function test_band_member_can_update_band_member_wihtout_revuew() {
		Event::fake();


		$this->actingAs($this->bandMember)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test'],
		])->assertRedirect();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'user_type' => BandMember::class,
			'id' => $this->bandMember->id
		]);
		$this->assertDatabaseHas('band_members', [
			'id' => $this->bandMember->user->id,
			'submitted' => false
		]);
        $bandMember = BandMember::find($this->bandMember->user->id);
        $this->assertEquals(collect(['test' => 'test']), $bandMember->data);
		Event::assertNotDispatched(BandMemberProfileFilled::class);
	}

	public function test_band_member_can_review_band_member() {
		Event::fake();

		$this->bandMember->user->photos()->save(BandMemberPhoto::factory()->make());

		$this->actingAs($this->bandMember)->patch(action('BandMember\BandMemberController@update', $this->bandMember->user), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'bandmember' => ['test' => 'test'],
			'review' => true
		])->assertRedirect();
		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'en',
			'user_type' => BandMember::class,
			'id' => $this->bandMember->id
		]);
		$this->assertDatabaseHas('band_members', [
			'id' => $this->bandMember->user->id,
			'submitted' => true
		]);
        $bandMember = BandMember::find($this->bandMember->user->id);
        $this->assertEquals(collect(['test' => 'test']), $bandMember->data);

		Event::assertDispatched(BandMemberProfileFilled::class, function ($event) {
			return $event->bandMember->id === $this->bandMember->user->id;
		});
	}

	public function test_notifies_band_member_when_profile_is_filled() {
		Notification::fake();

		event(new BandMemberProfileFilled($this->bandMember->user));

		Notification::assertSentTo($this->bandMember, ProfileFilledNotification::class);
	}
}
