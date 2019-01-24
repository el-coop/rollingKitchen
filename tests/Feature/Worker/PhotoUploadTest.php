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
	
	protected function setUp() {
		parent::setUp();
		
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->file = UploadedFile::fake()->image('photo.jpg');
		$this->workerPhoto = factory(WorkerPhoto::class)->create([
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
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
		$this->actingAs($this->kitchen)->post(action('Worker\WorkerController@storePhoto', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_can_upload_photo() {
		Crypt::shouldReceive('encrypt')->twice();
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
		Crypt::shouldReceive('encrypt')->twice();
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
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
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
		$this->assertDatabaseMissing('worker_photos',[
			'worker_id' => $this->worker->user_id,
			'file' => 'test.jpg'
		]);
	}
	
	public function test_admin_can_delete_own_photo() {
		$file = UploadedFile::fake()->image('test.jpg');
		$file->store('public/photos');
		
		$this->actingAs($this->admin)->delete(action('Worker\WorkerController@destroyPhoto', [
			'worker' => $this->worker->user,
			'photo' => $this->workerPhoto
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);
		
		Storage::disk('local')->assertMissing("public/photos/test.jpg");
		$this->assertDatabaseMissing('worker_photos',[
			'worker_id' => $this->worker->user_id,
			'file' => 'test.jpg'
		]);
	}
	
}
