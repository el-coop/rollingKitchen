<?php

namespace Admin\Kitchens\Stages;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Kitchen;
use App\Models\Stage;
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
	private $stage;

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
		$this->stage = Stage::factory()->create();
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\StageController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\StageController@index'))->assertForbidden();
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\StageController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\StageController@index'))->assertForbidden();
	}

	public function test_artist_manager_cant_see_page() {
		$this->actingAs($this->artistManager)->get(action('Admin\StageController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin)->get(action('Admin\StageController@index'))
			->assertSuccessful()
			->assertSee('</datatable>', false);
	}

	public function test_datatable_gets_data(){
		$response = $this->actingAs($this->admin)->get(action('DatatableController@list', ['table' => 'admin.stageTable', 'per_page' => 20]));
		$response->assertJsonFragment([
			'id' => $this->stage->id,
			'name' => $this->stage->name,
		]);
	}
}
