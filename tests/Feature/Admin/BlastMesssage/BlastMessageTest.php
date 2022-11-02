<?php

namespace Tests\Feature\Admin\BlastMesssage;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\User\MessageSentNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;

class BlastMessageTest extends TestCase {
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
			'payment_method' => 'individual'
		])->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_get_page() {
		$this->get(action('Admin\BlastMessageController@show'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_get_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_accountant_cant_get_page() {
		$this->actingAs($this->accountant)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_worker_cant_get_page() {
		$this->actingAs($this->worker)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_band_cant_get_page() {
		$this->actingAs($this->band)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_band_member_cant_get_page() {
		$this->actingAs($this->bandMember)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_artist_manager_cant_get_page() {
		$this->actingAs($this->artistManager)->get(action('Admin\BlastMessageController@show'))->assertForbidden();
	}

	public function test_admin_can_get_page() {
		$this->actingAs($this->admin)->get(action('Admin\BlastMessageController@show'))->assertSuccessful()->assertSee(__('admin/message.sms'));
	}

	public function test_guest_cant_send_message() {
		$this->post(action('Admin\BlastMessageController@send'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_send_message() {
		$this->actingAs($this->kitchen)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_accountant_cant_send_message() {
		$this->actingAs($this->accountant)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_worker_cant_send_message() {
		$this->actingAs($this->worker)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_band_cant_send_message() {
		$this->actingAs($this->band)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_band_member_cant_send_message() {
		$this->actingAs($this->bandMember)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_artist_manager_cant_send_message() {
		$this->actingAs($this->artistManager)->post(action('Admin\BlastMessageController@send'))->assertForbidden();
	}

	public function test_admin_can_send_message() {
		Notification::fake();
		$this->actingAs($this->admin)->post(action('Admin\BlastMessageController@send'), [
			'destination' => [Band::class => [], Worker::class => []],
			'text_en' => 'Test Text',
			'text_nl' => 'Test Text',
			'channels' => ['nexmo' => []],
		])->assertSuccessful();
		Notification::assertSentTo($this->band, MessageSentNotification::class);
		Notification::assertSentTo($this->bandMember, MessageSentNotification::class);
		Notification::assertSentTo($this->worker, MessageSentNotification::class);
		Notification::assertNotSentTo($this->kitchen, MessageSentNotification::class);
	}

	public function test_send_blast_message_validation() {
		$this->actingAs($this->admin)->post(action('Admin\BlastMessageController@send'), [
			'destination' => 'bla',
			'text_en' => 'Test Text',
			'channels' => ['mail' => []],
		])->assertSessionHasErrors([
			'destination',
			'text_nl',
			'subject_en',
			'subject_nl'
		]);
	}


}
