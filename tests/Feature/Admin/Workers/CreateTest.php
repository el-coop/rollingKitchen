<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Notifications\Worker\UserCreated;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {

	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	protected $worker;

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
		$this->workplaces = Workplace::factory(10)->create()->each(function ($workplace) {
			WorkFunction::factory(3)->make()->each(function ($workFunction) use ($workplace) {
				$workplace->workFunctions()->save($workFunction);
			});
		});
	}

	public function test_guest_cant_see_worker_form() {
		$this->get(action('Admin\WorkerController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_worker_form() {
		$this->actingAs($this->kitchen)->get(action('Admin\WorkerController@create'))->assertForbidden();
	}

	public function test_accountant_cant_see_worker_form() {
		$this->actingAs($this->accountant)->get(action('Admin\WorkerController@create'))->assertForbidden();
	}

	public function test_worker_cant_see_worker_form() {
		$this->actingAs($this->worker)->get(action('Admin\WorkerController@create'))->assertForbidden();
	}

	public function test_admin_can_see_worker_form() {
		$this->actingAs($this->admin)->get(action('Admin\WorkerController@create'))->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => null
			])->assertJsonFragment([
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => null
			]);
	}

	public function test_guest_cant_create_a_worker() {
		$this->post(action('Admin\WorkerController@store'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_a_worker() {
		$this->actingAs($this->kitchen)->post(action('Admin\WorkerController@store'))->assertForbidden();
	}

	public function test_accountant_cant_create_a_worker() {
		$this->actingAs($this->accountant)->post(action('Admin\WorkerController@store'))->assertForbidden();
	}

	public function test_worker_cant_create_a_worker() {
		$this->actingAs($this->worker)->post(action('Admin\WorkerController@store'))->assertForbidden();
	}

	public function test_admin_can_create_a_worker() {
		Notification::fake();
		$workplaces = $this->workplaces->random(2)->pluck('id');
		$worker = $this->actingAs($this->admin)->post(action('Admin\WorkerController@store'), [
			'name' => 'name',
			'email' => 'test@best.com',
			'type' => 0,
			'language' => 'en',
			'supervisor' => false,
			'workplaces' => $workplaces->toArray()
		])->assertSuccessful()->decodeResponseJson();

		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'test@best.com',
			'password' => '',
			'language' => 'en',
			'user_type' => Worker::class
		]);

		$this->assertDatabaseHas('workers', [
			'supervisor' => 0,
			'type' => 0,
		]);
		foreach ($workplaces as $workplace) {
			$this->assertDatabaseHas('worker_workplace', [
				'worker_id' => $worker['id'],
				'workplace_id' => $workplace,
			]);
		}
		Notification::assertSentTo(User::find($worker['user']['id']), UserCreated::class);
	}

	public function test_create_worker_validation() {
		$this->actingAs($this->admin)->post(action('Admin\WorkerController@store'), [
			'name' => '',
			'email' => 'test',
			'type' => 3,
			'language' => 'dl',
			'supervisor' => 'asd',
			'workplaces' => 'asd'
		])->assertRedirect()->assertSessionHasErrors([
			'name',
			'email',
			'type',
			'language',
			'supervisor',
			'workplaces'
		]);
	}
}
