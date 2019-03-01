<?php

namespace Tests\Feature\Admin\ArtistManager;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use App\Notifications\ArtistManager\UserCreated;
use Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;

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
	}

	public function test_guest_cant_create_artist_manager(){
		$this->get(action('Admin\ArtistManagerController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_artist_manager(){
		$this->actingAs($this->kitchen)->get(action('Admin\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_worker_cant_create_artist_manager(){
		$this->actingAs($this->worker)->get(action('Admin\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_artist_manager_cant_create_artist_manager(){
		$this->actingAs($this->artistManager)->get(action('Admin\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_accountant_cant_create_artist_manager(){
		$this->actingAs($this->accountant)->get(action('Admin\ArtistManagerController@create'))->assertForbidden();
	}

	public function test_admin_can_create_artist_manager(){
		$this->actingAs($this->admin)->get(action('Admin\ArtistManagerController@create'))
			->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'label' => __('global.name'),
				'type' => 'text',
				'value' => ""
			])->assertJsonFragment([
				'name' => 'email',
				'label' => __('global.email'),
				'type' => 'text',
				'value' => ""
			]);
	}

	public function test_guest_cant_store_artist_manager(){
		$this->post(action('Admin\ArtistManagerController@store'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_store_artist_manager(){
		$this->actingAs($this->kitchen)->post(action('Admin\ArtistManagerController@store'), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl'
		])->assertForbidden();
	}

	public function test_worker_cant_store_artist_manager(){
		$this->actingAs($this->worker)->post(action('Admin\ArtistManagerController@store'), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl'
		])->assertForbidden();
	}

	public function test_artist_manager_cant_store_artist_manager(){
		$this->actingAs($this->artistManager)->post(action('Admin\ArtistManagerController@store'), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl'
		])->assertForbidden();
	}

	public function test_accountant_cant_store_artist_manager(){
		$this->actingAs($this->accountant)->post(action('Admin\ArtistManagerController@store'), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl'
		])->assertForbidden();
	}

	public function test_admin_can_store_artist_manager(){
		Notification::fake();

		$this->actingAs($this->admin)->post(action('Admin\ArtistManagerController@store'), [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl'
		])->assertSuccessful()
			->assertJsonFragment([
				'name' => 'name',
				'email' => 'a@a.com',
			]);

		$this->assertDatabaseHas('users', [
			'name' => 'name',
			'email' => 'a@a.com',
			'language' => 'nl',
			'user_type' => ArtistManager::class
		]);
		$newArtistManager = User::where(['email' => 'a@a.com', 'user_type' => ArtistManager::class])->first()->user;
		$this->assertDatabaseHas('artist_managers', [
			'id' => $newArtistManager->id
		]);
		Notification::assertSentTo($newArtistManager->user, UserCreated::class);

	}
}
