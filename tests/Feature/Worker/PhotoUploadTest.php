<?php

namespace Tests\Feature\Worker;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use Crypt;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoUploadTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $kitchenPhoto;
	private $workerPhoto;
	private $file;

	protected function setUp(): void {
		parent::setUp();

		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->file = UploadedFile::fake()->image('photo.jpg');
		$this->workerPhoto = WorkerPhoto::factory()->create([
			'worker_id' => $this->worker->user->id,
			'file' => 'test.jpg'
		]);

	}

	public function test_guest_cant_upload_worker_photo() {

		$this->post(action('Worker\WorkerController@storePhoto', $this->worker->user))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_upload_worker_photo() {
		$this->actingAs($this->kitchen)->post(action('Worker\WorkerController@storePhoto', $this->worker->user))->assertForbidden();
	}

	public function test_other_worker_cant_upload_worker_photo() {
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);
		$this->actingAs($worker)->post(action('Worker\WorkerController@storePhoto', $this->worker->user))->assertForbidden();
	}

	public function test_worker_can_upload_photo() {
        Crypt::shouldReceive('getKey')->twice();
        Crypt::shouldReceive('encrypt')->times(3)->andReturn('');
        $this->actingAs($this->worker)->post(action('Worker\WorkerController@storePhoto', $this->worker->user), [
			'photo' => $this->file
		])->assertSuccessful()->assertJson([
			'worker_id' => $this->worker->user->id,
			'file' => $this->file->hashName()
		]);

		Storage::disk('local')->assertExists("public/photos/{$this->file->hashName()}");
		$this->assertDatabaseHas('worker_photos', [
			'worker_id' => $this->worker->user_id,
			'file' => $this->file->hashname()
		]);
	}

	public function test_admin_can_upload_photo() {
        Crypt::shouldReceive('getKey')->twice();
        Crypt::shouldReceive('encrypt')->times(3)->andReturn('');
        $this->actingAs($this->admin)->post(action('Worker\WorkerController@storePhoto', $this->worker->user), [
			'photo' => $this->file
		])->assertSuccessful()->assertJson([
			'worker_id' => $this->worker->user->id,
			'file' => $this->file->hashName()
		]);

		Storage::disk('local')->assertExists("public/photos/{$this->file->hashName()}");
		$this->assertDatabaseHas('worker_photos', [
			'worker_id' => $this->worker->user_id,
			'file' => $this->file->hashname()
		]);
	}

	public function test_guest_cant_delete_worker_photo() {
		$this->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_delete_worker_photo() {
		$this->actingAs($this->kitchen)->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertForbidden();
	}

	public function test_other_worker_cant_delete_worker_photo() {
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);
		$this->actingAs($worker)->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertForbidden();
	}

	public function test_worker_can_delete_own_photo() {
		$file = UploadedFile::fake()->image('test.jpg');
		$file->store('public/photos');

		$this->actingAs($this->worker)->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);

		Storage::disk('local')->assertMissing("public/photos/test.jpg");
		$this->assertDatabaseMissing('worker_photos', [
			'worker_id' => $this->worker->user_id,
			'file' => 'test.jpg'
		]);
	}

	public function test_admin_can_delete_worker_photo() {
		$file = UploadedFile::fake()->image('test.jpg');
		$file->store('public/photos');

		$this->workerPhoto->file = $file->hashName();
		$this->workerPhoto->save();

		$this->actingAs($this->admin)->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);

		Storage::disk('local')->assertMissing("public/photos/{$file->hashName()}");
		$this->assertDatabaseMissing('worker_photos', [
			'worker_id' => $this->worker->user_id,
			'file' => 'test.jpg'
		]);
	}

}
