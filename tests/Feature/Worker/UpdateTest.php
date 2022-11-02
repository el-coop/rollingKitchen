<?php

namespace Tests\Feature\Worker;

use App\Events\Worker\WorkerProfileFilled;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use App\Notifications\Worker\ProfileFilledNotification;
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
	protected $accountant;
	private $kitchenPhoto;
	private $workerPhoto;

	protected function setUp(): void {
		parent::setUp();

		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
	}

	public function test_guest_cant_see_worker_page() {
		$this->get(action('Worker\WorkerController@index', $this->worker->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_see_worker_page() {
		$this->actingAs($this->kitchen)->get(action('Worker\WorkerController@index', $this->worker->user))->assertForbidden();
	}

	public function test_other_worker_cant_see_worker_page() {
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);
		$this->actingAs($worker)->get(action('Worker\WorkerController@index', $this->worker->user))->assertForbidden();
	}

	public function test_worker_can_see_own_page() {
		$this->actingAs($this->worker)->get(action('Worker\WorkerController@index', $this->worker->user))->assertSuccessful();
	}

	public function test_accountant_cant_see_worker_page() {
		$this->actingAs($this->accountant)->get(action('Worker\WorkerController@index', $this->worker->user))->assertForbidden();
	}

	public function test_admin_can_see_worker_page() {
		$this->actingAs($this->admin)->get(action('Worker\WorkerController@index', $this->worker->user))->assertSuccessful();
	}

	public function test_guest_cant_update_worker() {
		$this->patch(action('Worker\WorkerController@update', $this->worker->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_update_worker() {
		$this->actingAs($this->kitchen)->patch(action('Worker\WorkerController@update', $this->worker->user))->assertForbidden();
	}

	public function test_accountant_cant_update_worker() {
		$this->actingAs($this->accountant)->patch(action('Worker\WorkerController@update', $this->worker->user))->assertForbidden();
	}

	public function test_other_worker_cant_update_worker() {
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);
		$this->actingAs($worker)->patch(action('Worker\WorkerController@update', $this->worker->user))->assertForbidden();
	}

	public function test_worker_can_update_self() {
		Event::fake();

		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			],
            'liability' => 'on'
		])->assertSessionHas('toast');

		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);

		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
			'submitted' => false,
            'liability' => true
		]);
        $worker = Worker::find($this->worker->user->id);
        $this->assertEquals(collect(['data']), $worker->data);

		Event::assertNotDispatched(WorkerProfileFilled::class);
	}

	public function test_worker_can_submit_self_when_has_photo_and_accepted_terms() {
		WorkerPhoto::factory()->create([
			'worker_id' => $this->worker->user->id
		]);

		Event::fake();

		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			],
			'review' => true,
            'liability' => 'on'
		])->assertSessionHas('toast');

		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);

		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
			'submitted' => true
		]);
        $worker = Worker::find($this->worker->user->id);
        $this->assertEquals(collect(['data']), $worker->data);

		Event::assertDispatched(WorkerProfileFilled::class, function ($event) {
			return $event->worker->id === $this->worker->user->id;
		});
	}

	public function test_worker_must_have_a_photo_and_accept_terms() {

		Event::fake();

		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			],
			'review' => true
		])->assertSessionHasErrors(['photos', 'liability']);

		$this->assertDatabaseMissing('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);

		$this->assertDatabaseMissing('workers', [
			'id' => $this->worker->user_id,
			'data' => json_encode(['data']),
			'submitted' => true
		]);

		Event::assertNotDispatched(WorkerProfileFilled::class, function ($event) {
			return $event->worker->id === $this->worker->user->id;
		});
	}


	public function test_worker_filled_notification_only_sent_once() {

		WorkerPhoto::factory()->create([
			'worker_id' => $this->worker->user->id
		]);

		Event::fake();

		$this->worker->user->submitted = true;
		$this->worker->user->save();

		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			],
			'review' => true,
            'liability' => 'on'
		])->assertSessionHas('toast');

		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en'
		]);

		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
			'submitted' => true
		]);
        $worker = Worker::find($this->worker->user->id);
        $this->assertEquals(collect(['data']), $worker->data);
		Event::assertNotDispatched(WorkerProfileFilled::class, function ($event) {
			return $event->worker->id === $this->worker->user->id;
		});
	}

	public function test_notifies_worker_when_profile_is_filled() {
		Notification::fake();

		event(new WorkerProfileFilled($this->worker->user));

		Notification::assertSentTo($this->worker, ProfileFilledNotification::class);
	}

	public function test_admin_can_update_worker() {
		$this->actingAs($this->admin)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
			'worker' => [
				'data'
			]
		])->assertSessionHas('toast');

		$this->assertDatabaseHas('users', [
			'user_id' => $this->worker->user_id,
			'user_type' => Worker::class,
			'email' => 'bla@gla.bla',
			'name' => 'game',
			'language' => 'en',
		]);

		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->user_id,
            'liability' => false
        ]);
		$worker = Worker::find($this->worker->user->id);
		$this->assertEquals(collect(['data']), $worker->data);
	}

	public function test_worker_update_validation() {
		$this->actingAs($this->worker)->patch(action('Worker\WorkerController@update', $this->worker->user), [
			'email' => 'bla',
			'name' => 'g',
			'language' => '',
			'worker' => 'test'
		])->assertSessionHasErrors([
			'email', 'name', 'language', 'worker'
		]);

	}
}
