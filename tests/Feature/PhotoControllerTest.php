<?php

namespace Tests\Feature;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\TaxReview;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoControllerTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $kitchenPhoto;
	private $workerPhoto;
	private $accountant;
	private $taxReview;
	
	protected function setUp(): void {
		parent::setUp();
		
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->kitchenPhoto = factory(Photo::class)->create([
			'kitchen_id' => $this->kitchen->user->id
		]);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->workerPhoto = factory(WorkerPhoto::class)->create([
			'worker_id' => $this->worker->user->id
		]);
		
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		
		$this->taxReview = factory(TaxReview::class)->create([
			'worker_id' => $this->worker->user->id
		]);
		
		Storage::fake('local');
	}
	
	public function test_anyone_can_get_kitchen_image() {
		$file = UploadedFile::fake()->create('demo.jpg');
		$file->store('public/photos');
		
		$this->kitchenPhoto->file = $file->hashName();
		$this->kitchenPhoto->save();
		
		$this->get($this->kitchenPhoto->url)->assertSuccessful();
	}
	
	public function test_guest_cant_see_worker_photo() {
		$this->get($this->workerPhoto->url)->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}
	
	public function test_kitchen_cant_see_worker_photo() {
		$this->actingAs($this->kitchen)->get($this->workerPhoto->url)->assertForbidden();
	}
	
	public function test_accountant_cant_see_worker_photo() {
		$this->actingAs($this->accountant)->get($this->workerPhoto->url)->assertForbidden();
	}
	
	public function test_other_worker_cant_see_worker_photo() {
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
		
		$this->actingAs($worker)->get($this->workerPhoto->url)->assertForbidden();
	}
	
	public function test_worker_can_see_own_decrypted_photo() {
		$file = UploadedFile::fake()->create('demo.jpg');
		$file->store('public/photos');
		
		$this->workerPhoto->file = $file->hashName();
		$this->workerPhoto->save();
		
		Crypt::shouldReceive('encrypt');
		Crypt::shouldReceive('decrypt')->once()->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');
		$this->withoutExceptionHandling();
		$this->actingAs($this->worker)->get($this->workerPhoto->url)->assertSuccessful();
	}
	
	public function test_admin_can_see_workers_decrypted_photo() {
		$file = UploadedFile::fake()->create('demo.jpg');
		$file->store('public/photos');
		
		$this->workerPhoto->file = $file->hashName();
		$this->workerPhoto->save();
		
		Crypt::shouldReceive('encrypt');
		Crypt::shouldReceive('decrypt')->once()->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');
		$this->actingAs($this->worker)->get($this->workerPhoto->url)->assertSuccessful();
	}
	
	
	public function test_guest_cant_see_tax_review() {
		$this->get($this->taxReview->url)->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}
	
	public function test_kitchen_cant_see_worker_review() {
		$this->actingAs($this->kitchen)->get($this->taxReview->url)->assertForbidden();
	}
	
	public function test_accountant_cant_see_tax_review() {
		$this->actingAs($this->accountant)->get($this->taxReview->url)->assertForbidden();
	}
	
	public function test_other_worker_cant_see_workers_tax_review() {
		$worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($worker);
		
		$this->actingAs($worker)->get($this->taxReview->url)->assertForbidden();
	}
	
	public function test_worker_can_see_own_decrypted_tax_review() {
		
		$file = UploadedFile::fake()->create('demo.pdf');
		$file->store('public/taxReviews');
		
		$this->taxReview->file = $file->hashName();
		$this->taxReview->save();
		
		Crypt::shouldReceive('encrypt');
		Crypt::shouldReceive('decrypt')->once()->with(Storage::get("public/taxReviews/{$this->taxReview->file}"))->andReturn('');
		$this->actingAs($this->worker)->get($this->taxReview->url)->assertSuccessful();
	}
	
	public function test_admin_can_see_workers_decrypted_tax_review() {
		
		$file = UploadedFile::fake()->create('demo.pdf');
		$file->store('public/taxReviews');
		
		$this->taxReview->file = $file->hashName();
		$this->taxReview->save();
		
		Crypt::shouldReceive('encrypt');
		Crypt::shouldReceive('decrypt')->once()->with(Storage::get("public/taxReviews/{$this->taxReview->file}"))->andReturn('');
		$this->withoutExceptionHandling();
		$this->actingAs($this->worker)->get($this->taxReview->url)->assertSuccessful();
	}
	
}
