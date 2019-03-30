<?php

namespace Tests\Feature\Admin\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandSchedule;
use App\Models\Kitchen;
use App\Models\Stage;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\Band\ConfirmationNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;

class ConfirmationEmailTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $schedule;
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
		factory(Band::class)->create([
			'payment_method' => 'individual'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->secondBand = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->secondBand);
		$stage = factory(Stage::class)->create();
		$this->band = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->band);
		$this->schedule = factory(BandSchedule::class)->create([
			'stage_id' => $stage->id,
			'band_id' => $this->band->user->id,
			'approved' => 'accepted',
			'payment' => 30
		]);
	}

	public function test_guest_cant_send_confirmation_email(){
		$this->post(action('Admin\BandController@sendConfirmation'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_send_confirmation_email(){
		$this->actingAs($this->kitchen)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_worker_cant_send_confirmation_email(){
		$this->actingAs($this->worker)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_band_cant_send_confirmation_email(){
		$this->actingAs($this->band)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_accountant_cant_send_confirmation_email(){
		$this->actingAs($this->accountant)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_artist_manager_cant_send_confirmation_email(){
		$this->actingAs($this->artistManager)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_band_member_cant_send_confirmation_email(){
		$this->actingAs($this->bandMember)->post(action('Admin\BandController@sendConfirmation'))->assertForbidden();
	}

	public function test_admin_can_send_confirmation_email(){
		Notification::fake();
		$this->actingAs($this->admin)->post(action('Admin\BandController@sendConfirmation'))->assertSuccessful();
		Notification::assertSentTo($this->band, ConfirmationNotification::class);
		Notification::assertNotSentTo($this->secondBand, ConfirmationNotification::class);

	}

}
