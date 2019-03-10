<?php

namespace Tests\Feature\Admin\Band;

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
use Carbon\Carbon;
use Event;
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
	
	protected function setUp() {
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
			->assertViewHas('schedules', BandSchedule::select('dateTime', 'stage_id as stage', 'band_id as band', 'payment', 'approved')->get()->groupBy('dateTime'));
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
	
	public function test_admin_can_post_new_schedule() {
		$bands = $this->bands->random(2);
		$stages = $this->stages->random(2);
		$dateTime = Carbon::now();
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->first()->id,
				'stage' => $stages->first()->id,
				'payment' => 10,
			], [
				'band' => $bands->last()->id,
				'stage' => $stages->last()->id,
				'payment' => 100,
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->first()->id,
			'stage_id' => $stages->first()->id,
			'payment' => 10,
			'dateTime' => $dateTime->format('Y-m-d H:i:00')
		]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->last()->id,
			'stage_id' => $stages->last()->id,
			'payment' => 100,
			'dateTime' => $dateTime->format('Y-m-d H:i:00')
		]);
	}
	
	public function test_shows_that_dont_appear_in_new_schedule_are_deleted_and_events_are_fired() {
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->random();
		$dateTime = Carbon::now();
		$oldShows = BandSchedule::all();
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $stage->id,
				'payment' => 10,
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'dateTime' => $dateTime->format('Y-m-d H:i:00')
		]);
		
		$oldShows->each(function ($show) use ($dateTime) {
			if ($show->dateTime != $dateTime->format('d/m/Y H:i')) {
				$this->assertDatabaseMissing('band_schedules', [
					'band' => $show->band_id,
					'stage' => $show->stage_id,
					'dateTime' => Carbon::createFromFormat('d/m/Y H:i', $show->dateTime)->format('Y-m-d H:i:00'),
				]);
				
				Event::assertDispatched(ShowDeleted::class, function ($event) use ($show) {
					return $event->show->id == $show->id;
				});
			}
		});
	}
	
	public function test_shows_that_appear_in_new_schedule_with_change_of_stage_fire_an_Event_and_keeps_approved_data() {
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->first();
		$dateTime = Carbon::now();
		
		$show = factory(BandSchedule::class)->create([
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'dateTime' => $dateTime->format('Y-m-d H:i:00'),
			'approved' => true,
		]);
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $this->stages->last()->id,
				'payment' => 10,
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->id,
			'stage_id' => $this->stages->last()->id,
			'payment' => 10,
			'dateTime' => $dateTime->format('Y-m-d H:i:00'),
			'approved' => true,
		]);
		
		Event::assertDispatched(ShowUpdated::class, function ($event) use ($show) {
			return $event->show->band_id == $show->band_id && $event->show->stage_id == $this->stages->last()->id && $event->show->payment == $show->payment && $event->show->dateTime == $show->dateTime;
		});
	}
	
	public function test_shows_that_appear_in_new_schedule_with_change_of_payment_fire_an_Event_and_reset_approved_data() {
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->first();
		$dateTime = Carbon::now();
		
		$show = factory(BandSchedule::class)->create([
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 10,
			'dateTime' => $dateTime->format('Y-m-d H:i:00'),
			'approved' => true,
		]);
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calendar' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $stage->id,
				'payment' => 5,
			]]
		]])->assertSuccessful()->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('band_schedules', [
			'band_id' => $bands->id,
			'stage_id' => $stage->id,
			'payment' => 5,
			'dateTime' => $dateTime->format('Y-m-d H:i:00'),
			'approved' => false,
		]);
		
		Event::assertDispatched(ShowUpdated::class, function ($event) use ($show) {
			return $event->show->band_id == $show->band_id && $event->show->stage_id == $show->stage_id && $event->show->payment == 5 && $event->show->dateTime == $show->dateTime;
		});
	}
	
	public function test_schedule_post_validation() {
		Event::fake();
		$bands = $this->bands->random();
		$stage = $this->stages->first();
		$dateTime = Carbon::now();
		
		
		$this->actingAs($this->admin)->post(action('Admin\BandController@storeSchedule'), ['calenda' => [
			$dateTime->format('d/m/Y H:i') => [[
				'band' => $bands->id,
				'stage' => $stage->id,
				'payment' => 5,
			]]
		]])->assertRedirect()->assertSessionHasErrors('calendar');
	}
}
