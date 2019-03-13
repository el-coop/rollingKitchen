<?php

namespace Tests\Feature\Admin\Workers;

use App\Events\Worker\TaxReviewUploaded;
use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\TaxReview;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\Worker\TaxReviewNotification;
use Crypt;
use Event;
use Illuminate\Http\UploadedFile;
use Notification;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaxReviewTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $accountant;
	private $taxReview;
	
	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		
		$this->taxReview = factory(TaxReview::class)->create([
			'worker_id' => $this->worker->user->id
		]);
	}
	
	public function test_guest_cant_upload_tax_review() {
		$this->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_kitchen_cant_upload_tax_review() {
		$this->actingAs($this->kitchen)->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user))->assertForbidden();
	}
	
	public function test_worker_cant_upload_tax_review() {
		$this->actingAs($this->worker)->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user))->assertForbidden();
	}
	
	public function test_accountant_cant_upload_tax_review() {
		$this->actingAs($this->accountant)->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user))->assertForbidden();
	}
	
	public function test_tax_review_validation() {
		$this->actingAs($this->admin)->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user), [
			'file' => 'tile',
			'name' => ''
		])->assertSessionHasErrors(['file', 'name']);
		
	}
	
	public function test_admin_can_upload_tax_review() {
		Event::fake();
		$this->withoutExceptionHandling();
		Storage::fake('local');
		Crypt::shouldReceive('encrypt')->twice();
		$file = UploadedFile::fake()->image('photo.jpg');
		
		$this->actingAs($this->admin)->post(action('Admin\WorkerController@storeTaxReview', $this->worker->user), [
			'file' => $file,
			'name' => 'Name'
		])->assertSuccessful()->assertJson([
			'worker_id' => $this->worker->user_id,
			'file' => $file->hashname(),
			'name' => 'Name'
		]);
		
		Storage::disk('local')->assertExists("public/taxReviews/{$file->hashName()}");
		$this->assertDatabaseHas('tax_reviews', [
			'worker_id' => $this->worker->user_id,
			'file' => $file->hashname(),
			'name' => 'Name'
		]);
		
		Event::assertDispatched(TaxReviewUploaded::class, function ($event) {
			return $event->worker->id == $this->worker->user->id;
		});
	}
	
	public function test_notifies_worker_when_tax_review_uploaded() {
		Notification::fake();
		
		event(new TaxReviewUploaded($this->worker->user));
		
		Notification::assertSentTo($this->worker, TaxReviewNotification::class);
	}
	
	public function test_guest_cant_delete_tax_review() {
		$this->delete(action('Admin\WorkerController@destroyTaxReview', [
			'worker' => $this->worker->user,
			'taxReview' => $this->taxReview
		]))->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}
	
	public function test_kitchen_cant_delete_tax_review() {
		$this->actingAs($this->kitchen)->delete(action('Admin\WorkerController@destroyTaxReview', [
			'worker' => $this->worker->user,
			'taxReview' => $this->taxReview
		]))->assertForbidden();
	}
	
	public function test_worker_cant_delete_tax_review() {
		$this->actingAs($this->worker)->delete(action('Admin\WorkerController@destroyTaxReview', [
			'worker' => $this->worker->user,
			'taxReview' => $this->taxReview
		]))->assertForbidden();
	}

	public function test_accountant_cant_delete_tax_review() {
		$this->actingAs($this->worker)->delete(action('Admin\WorkerController@destroyTaxReview', [
			'worker' => $this->worker->user,
			'taxReview' => $this->taxReview
		]))->assertForbidden();
	}
	
	
	public function test_admin_can_delete_tax_review() {
		Storage::fake('local');
		$file = UploadedFile::fake()->create('demo.pdf');
		$file->store('public/taxReviews');
		
		$this->taxReview->file = $file->hashName();
		$this->taxReview->save();
		
		$this->actingAs($this->admin)->delete(action('Admin\WorkerController@destroyTaxReview', [
			'worker' => $this->worker->user,
			'taxReview' => $this->taxReview
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);
		
		Storage::disk('local')->assertMissing("public/taxReviews/{$file->hashName()}");
		$this->assertDatabaseMissing('tax_reviews', [
			'id' => $this->taxReview->id,
			'worker_id' => $this->worker->user_id,
			'file' => 'demo.pdf',
			'name' => 'tax review 2019'
		]);
	}
	
}
