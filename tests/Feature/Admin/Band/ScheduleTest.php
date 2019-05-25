<?php

namespace Tests\Feature\Admin\Band;

use App\Events\Band\ShowCreated;
use App\Events\Band\ShowDeleted;
use App\Events\Band\ShowUpdated;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandSchedule;
use App\Models\Kitchen;
use App\Models\Stage;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\Band\ShowCreatedNotification;
use App\Notifications\Band\ShowDeletedNotification;
use App\Notifications\Band\ShowUpdatedNotification;
use Carbon\Carbon;
use ElCoop\Valuestore\Valuestore;
use Event;
use Storage;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $bands;
	protected $stages;
	
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
		
		$this->stages = factory(Stage::class, 4)->create();
		$this->bands = factory(Band::class, 4)->create()->each(function ($band) {
			$band->user()->save(factory(User::class)->make());
			$band->schedules()->save(factory(BandSchedule::class)->make([
				'stage_id' => $this->stages->random()->id
			]));
		});
		
		Storage::fake('local');
		Storage::disk('local')->put('test.valuestore.json', '');
		$path = Storage::path('test.valuestore.json');
		$this->app->singleton('settings', function ($app) use ($path) {
			return new Valuestore($path);
		});
	}
	
	public function test_guest_cant_access_schedule_page() {
		$this->get(action('Admin\BandController@schedule'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_access_schedule_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandController@schedule'))->assertForbidden();
	}
	
	public function test_worker_cant_access_schedule_page() {
		$this->actingAs($this->worker)->get(action('Admin\BandController@schedule'))->assertForbidden();
	}
	
	public function test_artist_manager_cant_access_schedule_page() {
		$this->actingAs($this->artistManager)->get(action('Admin\BandController@schedule'))->assertForbidden();
	}
	
	public function test_accountant_cant_access_schedule_page() {
		$this->actingAs($this->accountant)->get(action('Admin\BandController@schedule'))->assertForbidden();
	}
	
	public function test_band_cant_access_schedule_page() {
		$this->actingAs($this->bands->first()->user)->get(action('Admin\BandController@schedule'))->assertForbidden();
	}
	
	public function test_admin_can_access_schedule_page() {
		$this->actingAs($this->admin)->get(action('Admin\BandController@schedule'))->assertSuccessful()
			->assertViewIs('admin.bands.schedule')
			->assertViewHas('stages', $this->stages->pluck('name', 'id'))
			->assertViewHas('bands', $this->bands->pluck('user.name', 'id'))
			->assertViewHas('schedules', BandSchedule::select('date_time', 'end_time', 'stage_id as stage', 'band_id as band', 'payment', 'approved')->get()->groupBy('date_time'));
	}
	
	public function test_guest_cant_post_new_schedule() {
		$this->post(action('Admin\BandController@storeSchedule'))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_post_new_schedule() {
		$this->actingAs($this->kitchen)->post(action('Admin\BandController@storeSchedule'))->assertForbidden();
	}
	
	public function test_worker_cant_post_new_schedule() {
		$this->actingAs($this->worker)->post(action('Admin\BandController@storeSchedule'))->assertForbidden();
	}
	
	public function test_artist_manager_cant_post_new_schedule() {
		$this->actingAs($this->artistManager)->post(action('Admin\BandController@storeSchedule'))->assertForbidden();
	}
	
	public function test_accountant_cant_post_new_schedule() {
		$this->actingAs($this->accountant)->post(action('Admin\BandController@storeSchedule'))->assertForbidden();
	}
	
	public function test_band_cant_post_new_schedule() {
		$this->actingAs($this->bands->first()->user)->post(action('Admin\BandController@storeSchedule'))->assertForbidden();
	}
	
	public function test_admin_can_post_new_schedule_and_fires_event() {
		Event::fake();
		app('settings')->put('schedule_budget', 120);
		$bands = $this->bands->random(2);
		$stages = $this->stages->random(2);
		$dateTime = Carbon::today()->addHours(21);
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->first()->id,
				'stage' => $stages->first()->id,
				'payment' => 10,
				'endTime' => $dateTime->addHour(1)->format('H:i')
			], [
				'band' => $bands->last()->id,
				'stage' => $stages->last()->id,
				'payment' => 100,
				'endTime' => $dateTime->format('H:i')
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->first()->id,
			'stage_id' => $stages->first()->id,
			'payment' => 10,
			'date_time' => $dateTime->subHour()->format('Y-m-d H:i:00'),
			'end_time' => $dateTime->addHour()->format('Y-m-d H:i:00')
		]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->last()->id,
			'stage_id' => $stages->last()->id,
			'payment' => 100,
			'date_time' => $dateTime->subHour()->format('Y-m-d H:i:00'),
			'end_time' => $dateTime->addHour()->format('Y-m-d H:i:00')
		]);
		Event::assertDispatched(ShowCreated::class, function ($event) use ($bands, $stages) {
			return $event->show->band->id == $bands->first()->id && $event->show->stage->id == $stages->first()->id && $event->show->payment == 10;
		});
	}
	
	public function test_notifies_bands_on_show_created_event() {
		Notification::fake();
		
		$band = $this->bands->first();
		
		event(new ShowCreated($band->schedules->first()));
		
		Notification::assertSentTo($band->user, ShowCreatedNotification::class);
	}
	
	public function test_admin_cant_post_over_budget_schedule() {
		app('settings')->put('schedule_budget', 100);
		$bands = $this->bands->random(2);
		$stages = $this->stages->random(2);
		$dateTime = Carbon::today()->addHours(21);
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->first()->id,
				'stage' => $stages->first()->id,
				'payment' => 10,
				'endTime' => $dateTime->addHour(1)->format('H:i')
			], [
				'band' => $bands->last()->id,
				'stage' => $stages->last()->id,
				'payment' => 100,
				'endTime' => $dateTime->addHour(1)->format('H:i')
			]]
		]])->assertRedirect()->assertSessionHasErrors('payment');
		
		$this->assertDatabaseMissing('band_schedules', [
			'band_id' => $bands->first()->id,
			'stage_id' => $stages->first()->id,
			'payment' => 10,
			'date_time' => $dateTime->format('Y-m-d H:i')
		]);
		
		$this->assertDatabaseMissing('band_schedules', [
			'band_id' => $bands->last()->id,
			'stage_id' => $stages->last()->id,
			'payment' => 100,
			'date_time' => $dateTime->format('Y-m-d H:i')
		]);
	}
	
	public function test_shows_that_dont_appear_in_new_schedule_are_deleted_and_events_are_fired() {
		app('settings')->put('schedule_budget', 120);
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->random();
		$dateTime = Carbon::today()->addHours(21);
		$oldShows = BandSchedule::all();
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $stage->id,
				'payment' => 10,
				'endTime' => $dateTime->addHour()->format('H:i')
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'date_time' => $dateTime->subHour()->format('Y-m-d H:i:00'),
			'end_time' => $dateTime->addHour()->format('Y-m-d H:i:00')
		]);
		
		$oldShows->each(function ($show) use ($dateTime) {
			if ($show->date_time != $dateTime->format('d/m/Y H:i')) {
				$this->assertDatabaseMissing('band_schedules', [
					'band' => $show->band_id,
					'stage' => $show->stage_id,
					'date_time' => Carbon::createFromFormat('d/m/Y H:i', $show->date_time)->format('Y-m-d H:i:00'),
				]);
				
				Event::assertDispatched(ShowDeleted::class, function ($event) use ($show) {
					return $event->show->id == $show->id;
				});
			}
		});
	}
	
	public function test_notifies_users_on_show_deleted_event() {
		Notification::fake();
		
		$band = $this->bands->first();
		
		event(new ShowDeleted($band->schedules->first()));
		
		Notification::assertSentTo($band->user, ShowDeletedNotification::class);
	}
	
	public function test_shows_that_appear_in_new_schedule_with_change_of_stage_fire_an_Event_and_keeps_approved_data() {
		app('settings')->put('schedule_budget', 120);
		Event::fake();
		$band = $this->bands->random();
		$stage = $this->stages->first();
		$dateTime = Carbon::today()->addHours(21);
		
		$show = factory(BandSchedule::class)->create([
			'band_id' => $band->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'date_time' => $dateTime->format('Y-m-d H:i'),
			'approved' => 'accepted',
		]);
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $band->id,
				'stage' => $this->stages->last()->id,
				'payment' => 10,
				'endTime' => $dateTime->addHour()->format('H:i'),
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $band->id,
			'stage_id' => $this->stages->last()->id,
			'payment' => 10,
			'date_time' => $dateTime->subHour()->format('Y-m-d H:i:00'),
			'end_time' => $dateTime->addHour()->format('Y-m-d H:i:00'),
			'approved' => 'accepted',
		]);
		
		Event::assertDispatched(ShowUpdated::class, function ($event) use ($show) {
			return $event->show->band_id == $show->band_id && $event->show->stage_id == $this->stages->last()->id && $event->show->payment == $show->payment && $event->show->date_time == $show->date_time;
		});
	}
	
	public function test_shows_that_appear_in_new_schedule_with_change_of_payment_fire_an_Event_and_reset_approved_data() {
		app('settings')->put('schedule_budget', 120);
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->first();
		$dateTime = Carbon::today()->addHours(21);
		
		$show = factory(BandSchedule::class)->create([
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'date_time' => $dateTime->format('Y-m-d H:i'),
			'approved' => 'accepted',
		]);
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $stage->id,
				'payment' => 5,
				'endTime' => $dateTime->addHour()->format('H:i'),
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 5,
			'date_time' => $dateTime->subHour()->format('Y-m-d H:i:00'),
			'end_time' => $dateTime->addHour()->format('Y-m-d H:i:00'),
			'approved' => 'pending',
		]);
		
		Event::assertDispatched(ShowUpdated::class, function ($event) use ($show) {
			return $event->show->band_id == $show->band_id && $event->show->stage_id == $show->stage_id && $event->show->payment == 5 && $event->show->date_time == $show->date_time;
		});
	}
	
	public function test_notifies_bands_on_show_updated_event() {
		Notification::fake();
		
		$band = $this->bands->first();
		
		event(new ShowUpdated($band->schedules->first(), factory(BandSchedule::class)->make([
			'band_id' => $band->id,
			'stage_id' => $this->stages->random()->id,
			'payment' => 10,
			'date_time' => $band->schedules->first()->date_time,
			'approved' => 'accepted',
		])));
		
		Notification::assertSentTo($band->user, ShowUpdatedNotification::class);
	}
	
	public function test_schedule_post_validation() {
		Event::fake();
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => 'bla']
		)->assertRedirect()->assertSessionHasErrors('calendar');
	}
}
