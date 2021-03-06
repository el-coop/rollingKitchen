<?php

namespace Tests\Feature\Admin\KitchenExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\KitchenExportColumn;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandMember;
	protected $kitchenExportColumns;

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
		$this->band = factory(User::class)->make();
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$i = 1;
		$this->kitchenExportColumns = factory(KitchenExportColumn::class,4)->make()->each(function ($bandExportColumn) use ($i) {
			$bandExportColumn->column = 'user.email';
			$i = $i + 1;
			$bandExportColumn->order = $i;
			$bandExportColumn->save();
		});
	}

	public function test_guest_cant_create_band_payment_column() {
		$this->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_create_band_payment_column() {
		$this->actingAs($this->kitchen)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_worker_cant_create_band_payment_column() {
		$this->actingAs($this->worker)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_band_cant_create_band_payment_column() {
		$this->actingAs($this->band)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_accountant_cant_create_band_payment_column() {
		$this->actingAs($this->accountant)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_band_member_cant_create_band_payment_column() {
		$this->actingAs($this->bandMember)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_artist_manager_cant_create_band_payment_column() {
		$this->actingAs($this->artistManager)->patch(action('Admin\KitchenExportColumnController@saveOrder'))->assertForbidden();
	}

	public function test_admin_can_create_band_payment_column() {
		$newOrder = $this->kitchenExportColumns->pluck('id')->shuffle()->toArray();
		$this->actingAs($this->admin)->patch(action('Admin\KitchenExportColumnController@saveOrder'), [
			'order' => $newOrder
		])->assertSuccessful();

		$order = KitchenExportColumn::orderBy('order')->get()->pluck('id')->toArray();
		$this->assertEquals($newOrder,$order);
	}
}
