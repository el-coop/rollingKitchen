<?php

namespace Tests\Feature\Band\BandAdmin;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandAdmin;
use App\Models\BandAdminPhoto;
use App\Models\BandMember;
use App\Models\Kitchen;
use Crypt;
use Illuminate\Http\UploadedFile;
use Storage;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $bandAdminPhoto;
	private $file;
	private $bandAdmin;
	private $bandMember;
	private $accountant;
	private $band;
	private $artistManager;

	protected function setUp(): void {
		parent::setUp();

		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->artistManager = factory(User::class)->make();
		factory(ArtistManager::class)->create()->user()->save($this->artistManager);
		$this->band = factory(User::class)->make();
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
		$this->bandAdmin = factory(BandAdmin::class)->make();
		$this->band->user->admin()->save($this->bandAdmin);
		$this->file = UploadedFile::fake()->image('photo.jpg');
		$this->bandAdminPhoto = factory(BandAdminPhoto::class)->create([
			'band_admin_id' => $this->bandAdmin->id,
			'file' => 'test.jpg'
		]);

	}

	public function test_guest_cant_upload_band_admin_photo() {
		$this->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_upload_band_admin_photo() {
		$this->actingAs($this->kitchen)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_worker_cant_upload_band_admin_photo() {
		$this->actingAs($this->worker)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_accountant_cant_upload_band_admin_photo() {
		$this->actingAs($this->accountant)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_other_band_cant_upload_band_admin_photo() {
		$band = factory(User::class)->make();
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->actingAs($band)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_artist_manager_cant_upload_band_admin_photo() {
		$this->actingAs($this->artistManager)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_band_member_cant_upload_band_admin_photo() {
		$this->actingAs($this->bandMember)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]))->assertForbidden();
	}

	public function test_admin_cant_upload_photo() {
		$this->actingAs($this->admin)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]), [
			'photo' => $this->file
		])->assertForbidden();
	}

	public function test_band_can_upload_photo() {
		Crypt::shouldReceive('encrypt')->twice();
		$this->actingAs($this->band)->post(action('Band\BandAdminController@storePhoto', [$this->band->user, $this->bandAdmin]), [
			'photo' => $this->file
		])->assertSuccessful()->assertJson([
			'band_admin_id' => $this->bandAdmin->id,
			'file' => $this->file->hashName()
		]);

		Storage::disk('local')->assertExists("public/photos/{$this->file->hashName()}");
		$this->assertDatabaseHas('band_admin_photos', [
			'band_admin_id' => $this->bandAdmin->id,
			'file' => $this->file->hashname()
		]);
	}

	public function test_guest_cant_delete_band_admin_photo() {
		$this->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto
		]))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_delete_band_admin_photo() {
		$this->actingAs($this->kitchen)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto
		]))->assertForbidden();
	}

	public function test_accountant_cant_delete_band_admin_photo() {
		$this->actingAs($this->accountant)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertForbidden();
	}

	public function test_worker_cant_delete_band_admin_photo() {
		$this->actingAs($this->worker)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_band_admin_photo() {
		$this->actingAs($this->artistManager)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertForbidden();
	}

	public function test_band_member_cant_delete_band_admin_photo() {

		$this->actingAs($this->bandMember)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertForbidden();
	}

	public function test_admin_cant_delete_band_admin_photo() {
		$this->actingAs($this->admin)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertForbidden();
	}

	public function test_band_can_delete_own_photo() {
		$file = UploadedFile::fake()->image('test.jpg');
		$file->store('public/photos');

		$this->actingAs($this->band)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto

		]))->assertSuccessful()->assertJson([
			'success' => true
		]);

		Storage::disk('local')->assertMissing("public/photos/test.jpg");
		$this->assertDatabaseMissing('band_admin_photos', [
			'band_admin_id' => $this->bandAdmin->id,
			'file' => 'test.jpg'
		]);
	}

	public function test_other_band_cant_destroy_band_admin_photo() {
		$band = factory(User::class)->make();
		factory(Band::class)->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->actingAs($band)->delete(action('Band\BandAdminController@destroyPhoto', [
			'band' => $this->band->user,
			'bandAdmin' => $this->bandAdmin,
			'photo' => $this->bandAdminPhoto
		]))->assertForbidden();
	}
}
