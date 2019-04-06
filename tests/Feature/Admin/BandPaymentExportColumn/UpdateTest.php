<?php

namespace Tests\Feature\Admin\BandPaymentExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandPaymentExportColumn;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $bandPaymentColumn;

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
			'payment_method' => 'individual'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->bandPaymentColumn = factory(BandPaymentExportColumn::class)->create([
			'column' => 'user.name',
			'order' => 0
		]);
	}

	public function test_guest_cant_create_band_payment_column() {
		$this->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_band_payment_column() {
		$this->actingAs($this->kitchen)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_worker_cant_create_band_payment_column() {
		$this->actingAs($this->worker)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_band_cant_create_band_payment_column() {
		$this->actingAs($this->band)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_accountant_cant_create_band_payment_column() {
		$this->actingAs($this->accountant)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_band_member_cant_create_band_payment_column() {
		$this->actingAs($this->bandMember)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_artist_manager_cant_create_band_payment_column() {
		$this->actingAs($this->artistManager)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn))->assertForbidden();
	}

	public function test_admin_can_create_band_payment_column() {
		$this->actingAs($this->admin)->patch(action('Admin\BandPaymentExportColumnController@update', $this->bandPaymentColumn), [
			'name' => 'test',
			'column' => 'user.email'
		])->assertSuccessful();

		$this->assertDatabaseHas('band_payment_export_columns', [
			'id' => $this->bandPaymentColumn->id,
			'name' => 'test',
			'column' => 'user.email'
		]);
	}
}
