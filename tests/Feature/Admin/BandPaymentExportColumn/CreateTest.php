<?php

namespace Tests\Feature\Admin\BandPaymentExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;

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
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_create_band_payment_column() {
		$this->post(action('Admin\BandPaymentExportColumnController@create'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_band_payment_column() {
		$this->actingAs($this->kitchen)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_worker_cant_create_band_payment_column() {
		$this->actingAs($this->worker)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_band_cant_create_band_payment_column() {
		$this->actingAs($this->band)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_accountant_cant_create_band_payment_column() {
		$this->actingAs($this->accountant)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_band_member_cant_create_band_payment_column() {
		$this->actingAs($this->bandMember)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_artist_manager_cant_create_band_payment_column() {
		$this->actingAs($this->artistManager)->post(action('Admin\BandPaymentExportColumnController@create'))->assertForbidden();
	}

	public function test_admin_can_create_band_payment_column() {
		$this->actingAs($this->admin)->post(action('Admin\BandPaymentExportColumnController@create'), [
			'column' => 'user.name',
		])->assertSuccessful();

		$this->assertDatabaseHas('band_payment_export_columns', [
			'column' => 'user.name',
		]);
	}

	public function test_create_band_payment_column_validation() {
		$this->actingAs($this->admin)->post(action('Admin\BandPaymentExportColumnController@create'), [
			'column' => 'name',
		])->assertSessionHasErrors(['column']);
	}
}
