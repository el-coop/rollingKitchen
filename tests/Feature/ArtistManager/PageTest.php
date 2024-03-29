<?php

namespace Tests\Feature\ArtistManager;

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
		Band::factory()->create()->user()->save($this->band);
	}

	public function test_guest_cant_see_page(){
		$this->get(action('ArtistManager\ArtistManagerController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page(){
		$this->actingAs($this->kitchen)->get(action('ArtistManager\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_worker_cant_see_page(){
		$this->actingAs($this->worker)->get(action('ArtistManager\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page(){
		$this->actingAs($this->accountant)->get(action('ArtistManager\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_band_cant_see_page(){
		$this->actingAs($this->band)->get(action('ArtistManager\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_admin_cant_see_page(){
		$this->actingAs($this->admin)->get(action('ArtistManager\ArtistManagerController@index'))->assertForbidden();
	}

	public function test_artist_manager_can_see_page(){
		$this->actingAs($this->artistManager)->get(action('ArtistManager\ArtistManagerController@index'))
			->assertSuccessful()
			->assertSee('</datatable>', false);
	}

	public function est_datatable_gets_dat(){
		$response = $this->actingAs($this->artistManager)->get(action('DatatableController@artistManagerList', ['table' => 'artistManager.bandsTable', 'per_page' => 20]));
		$response->assertJsonFragment([
			'id' => $this->band->user->id,
			'name' => $this->band->name,
			'email' => $this->band->email
		]);
	}
}
