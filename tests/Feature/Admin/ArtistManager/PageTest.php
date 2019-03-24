<?php

namespace Tests\Feature\Admin\ArtistManager;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;

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
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\ArtistManagerController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_artist_manager_cant_see_page() {
		$this->actingAs($this->artistManager)->get(action('Admin\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin)->get(action('Admin\ArtistManagerController@index'))
			->assertSuccessful()
			->assertSee('</datatable>');
	}

	public function test_datatable_gets_data(){
		$response = $this->actingAs($this->admin)->get(action('DatatableController@list', ['table' => 'admin.artistManagerTable', 'per_page' => 20]));
		$response->assertJsonFragment([
			'id' => $this->artistManager->user->id,
			'name' => $this->artistManager->name,
			'email' => $this->artistManager->email
		]);
	}
}
