<?php

namespace Tests\Feature\Band;

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
use Illuminate\Support\Facades\Storage;
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
	protected $band;
	protected $bandMember;
	protected $schedule;

	/**
	 *
	 */
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

		$stage = factory(Stage::class)->create();
		$this->band = factory(User::class)->make();
		factory(Band::class)->create()->user()->save($this->band);
		$this->schedule = factory(BandSchedule::class)->create([
			'stage_id' => $stage->id,
			'band_id' => $this->band->user->id,
		]);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_user_cant_accept_schedule(){
		$this->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_accept_schedule(){
		$this->actingAs($this->kitchen)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_worker_cant_accept_schedule(){
		$this->actingAs($this->worker)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_accountant_cant_accept_schedule(){
		$this->actingAs($this->accountant)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_band_member_cant_accept_schedule(){
		$this->actingAs($this->bandMember)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_artist_manager_cant_accept_schedule(){
		$this->actingAs($this->artistManager)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_admin_cant_accept_schedule(){
		$this->actingAs($this->admin)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_band_cant_accept_schedule(){
		$this->actingAs($this->band)->patch(action('Band\BandController@approveSchedule', [$this->band->user, $this->schedule]))
			->assertSuccessful()
			->assertJsonFragment([
				'stage' => $this->schedule->stage->name,
				'date_time' => $this->schedule->date_time,
				'approved' => 'accepted'
			]);
		$this->assertDatabaseHas('band_schedules', [
			'id' => $this->schedule->id,
			'approved' => 'accepted'
		]);
	}

	public function test_user_cant_reject_schedule(){
		$this->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_reject_schedule(){
		$this->actingAs($this->kitchen)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_worker_cant_reject_schedule(){
		$this->actingAs($this->worker)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_accountant_cant_reject_schedule(){
		$this->actingAs($this->accountant)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_band_member_cant_reject_schedule(){
		$this->actingAs($this->bandMember)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_artist_manager_cant_reject_schedule(){
		$this->actingAs($this->artistManager)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_admin_cant_reject_schedule(){
		$this->actingAs($this->admin)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))->assertForbidden();
	}

	public function test_band_cant_reject_schedule(){
		$this->actingAs($this->band)->patch(action('Band\BandController@rejectSchedule', [$this->band->user, $this->schedule]))
			->assertSuccessful()
			->assertJsonFragment([
				'stage' => $this->schedule->stage->name,
				'date_time' => $this->schedule->date_time,
				'approved' => 'rejected'
			]);
		$this->assertDatabaseHas('band_schedules', [
			'id' => $this->schedule->id,
			'approved' => 'rejected'
		]);
	}
	
}
