<?php

namespace Tests\Feature\BandMember;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandMemberPhoto;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Crypt;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $bandMemberPhoto;
	private $file;
	private $bandMember;
	private $accountant;
	private $band;
	private $artistManager;

	protected function setUp(): void {
		parent::setUp();

		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->artistManager = User::factory()->make();
		ArtistManager::factory()->create()->user()->save($this->artistManager);
		$this->band = User::factory()->make();
		Band::factory()->create([
			'payment_method' => 'band'
		])->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);

		$this->file = UploadedFile::fake()->image('photo.jpg');
		$this->bandMemberPhoto = BandMemberPhoto::factory()->create([
			'band_member_id' => $this->bandMember->user->id,
			'file' => 'test.jpg'
		]);

	}

	public function test_guest_cant_upload_band_member_photo() {
		$this->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_upload_band_member_photo() {
		$this->actingAs($this->kitchen)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_worker_cant_upload_band_member_photo() {
		$this->actingAs($this->worker)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_accountant_cant_upload_band_member_photo() {
		$this->actingAs($this->accountant)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_cant_upload_band_member_photo() {
		$this->actingAs($this->band)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_artist_manager_cant_upload_band_member_photo() {
		$this->actingAs($this->artistManager)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_other_band_member_cant_upload_band_member_photo() {
		$bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($bandMember);
		$this->actingAs($bandMember)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user))->assertForbidden();
	}

	public function test_admin_cant_upload_photo() {
		$this->actingAs($this->admin)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user), [
			'photo' => $this->file
		])->assertForbidden();
	}

	public function test_band_member_can_upload_photo() {
		Crypt::shouldReceive('getKey')->twice();
        Crypt::shouldReceive('encrypt')->times(3)->andReturn('');
		$this->actingAs($this->bandMember)->post(action('BandMember\BandMemberController@storePhoto', $this->bandMember->user), [
			'photo' => $this->file
		])->assertSuccessful()->assertJson([
			'band_member_id' => $this->bandMember->user->id,
			'file' => $this->file->hashName()
		]);

		Storage::disk('local')->assertExists("public/photos/{$this->file->hashName()}");
		$this->assertDatabaseHas('band_member_photos', [
			'band_member_id' => $this->bandMember->user_id,
			'file' => $this->file->hashname()
		]);
	}

	public function test_guest_cant_delete_band_member_photo() {
		$this->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_delete_band_member_photo() {
		$this->actingAs($this->kitchen)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_accountant_cant_delete_band_member_photo() {
		$this->actingAs($this->accountant)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_worker_cant_delete_band_member_photo() {
		$this->actingAs($this->worker)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_band_cant_delete_band_member_photo() {
		$this->actingAs($this->band)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_artist_manager_cant_delete_band_member_photo() {
		$this->actingAs($this->artistManager)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_other_band_member_cant_delete_band_member_photo() {
		$bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($bandMember);
		$this->actingAs($bandMember)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_admin_cant_delete_band_member_photo() {
		$this->actingAs($this->admin)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertForbidden();
	}

	public function test_band_member_can_delete_own_photo() {
		$file = UploadedFile::fake()->image('test.jpg');
		$file->store('public/photos');

		$this->actingAs($this->bandMember)->delete(action('BandMember\BandMemberController@destroyPhoto', [
			'bandMember' => $this->bandMember->user,
			'photo' => $this->bandMemberPhoto
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);

		Storage::disk('local')->assertMissing("public/photos/test.jpg");
		$this->assertDatabaseMissing('band_member_photos', [
			'band_member_id' => $this->bandMember->user_id,
			'file' => 'test.jpg'
		]);
	}

}
