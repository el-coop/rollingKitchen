<?php

namespace Tests\Feature\Admin\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
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
	protected $band;

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
		factory(Band::class)->create()->user()->save($this->band);
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\BandController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandController@index'))->assertForbidden();
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\BandController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\BandController@index'))->assertForbidden();
	}

	public function test_artist_manager_cant_see_page() {
		$this->actingAs($this->artistManager)->get(action('Admin\BandController@index'))->assertForbidden();
	}

	public function test_band_cant_see_page() {
		$this->actingAs($this->band)->get(action('Admin\BandController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin)->get(action('Admin\BandController@index'))
			->assertSuccessful()
			->assertSee('</datatable>');
	}

	public function test_datatable_gets_data(){
		$response = $this->actingAs($this->admin)->get(action('DatatableController@list', ['table' => 'admin.bandsTable', 'per_page' => 20]));
		$response->assertJsonFragment([
			'id' => "{$this->band->user->id}",
			'name' => $this->band->name,
			'email' => $this->band->email
		]);
	}
}
