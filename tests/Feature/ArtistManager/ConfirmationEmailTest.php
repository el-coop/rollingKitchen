<?php

namespace Tests\Feature\ArtistManager;

use App\Listeners\Band\SendShowCreatedNotification;
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
use ElCoop\Valuestore\Valuestore;
use Illuminate\Support\Facades\Storage;
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
		$this->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_send_confirmation_email(){
		$this->actingAs($this->kitchen)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_worker_cant_send_confirmation_email(){
		$this->actingAs($this->worker)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_band_cant_send_confirmation_email(){
		$this->actingAs($this->band)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_accountant_cant_send_confirmation_email(){
		$this->actingAs($this->accountant)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_admin_cant_send_confirmation_email(){
		$this->actingAs($this->admin)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_band_member_cant_send_confirmation_email(){
		$this->actingAs($this->bandMember)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertForbidden();
	}

	public function test_artist_manager_can_send_confirmation_email(){
		Notification::fake();
		$this->actingAs($this->artistManager)->post(action('ArtistManager\ArtistManagerController@sendConfirmation'))->assertSuccessful();
		Notification::assertSentTo($this->band, ConfirmationNotification::class);
		Notification::assertNotSentTo($this->secondBand, ConfirmationNotification::class);

	}

	public function test_guest_cant_edit_confirmation_email(){
		$this->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_edit_confirmation_email(){
		$this->actingAs($this->kitchen)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_worker_cant_edi_confirmation_email(){
		$this->actingAs($this->worker)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_band_cant_update_confirmation_email(){
		$this->actingAs($this->band)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_accountant_cant_update_confirmation_email(){
		$this->actingAs($this->accountant)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_admin_cant_edit_confirmation_email(){
		$this->actingAs($this->admin)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_band_member_cant_edit_confirmation_email(){
		$this->actingAs($this->bandMember)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'))->assertForbidden();
	}

	public function test_artist_manager_can_edit_confirmation_email(){
		Storage::fake('local');
		Storage::disk('local')->put('test.valuestore.json', '');
		$path = Storage::path('test.valuestore.json');
		$this->app->singleton('settings', function ($app) use ($path) {
			return new Valuestore($path);
		});
		$faker = $this->faker;
		$settings = app('settings');
		$settings->put('bands_confirmation_subject_nl', $faker->text);
		$settings->put('bands_confirmation_subject_en', $faker->text);
		$settings->put('bands_confirmation_text_nl', $faker->text);
		$settings->put('bands_confirmation_text_en', $faker->text);
		$this->actingAs($this->artistManager)->patch(action('ArtistManager\ArtistManagerController@updateConfirmationEmail'), [
				'subject_en' => 'test 1',
				'subject_nl' => 'test 2',
				'text_en' => 'test 3',
				'text_nl' => 'test 4',
		])->assertSuccessful();
		$this->assertEquals('test 1', $settings->get('bands_confirmation_subject_en'));
		$this->assertEquals('test 2', $settings->get('bands_confirmation_subject_nl'));
		$this->assertEquals('test 3', $settings->get('bands_confirmation_text_en'));
		$this->assertEquals('test 4', $settings->get('bands_confirmation_text_nl'));
	}

}
