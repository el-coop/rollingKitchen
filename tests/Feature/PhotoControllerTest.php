<?php

namespace Tests\Feature;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandMemberPhoto;
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
	private $bandMember;
	private $bandMemberPhoto;

	protected function setUp(): void {
		parent::setUp();

		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->kitchenPhoto = Photo::factory()->create([
			'kitchen_id' => $this->kitchen->user->id
		]);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->workerPhoto = WorkerPhoto::factory()->create([
			'worker_id' => $this->worker->user->id
		]);


		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => Band::factory()->create()->id
		])->user()->save($this->bandMember);
		$this->bandMemberPhoto = BandMemberPhoto::factory()->create([
			'band_member_id' => $this->bandMember->user->id
		]);

		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);

		$this->taxReview = TaxReview::factory()->create([
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
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);

		$this->actingAs($worker)->get($this->workerPhoto->url)->assertForbidden();
	}

	public function test_worker_can_see_own_decrypted_photo() {
        Crypt::shouldReceive('getKey')->twice()
        ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.jpg');
		$file->store('public/photos');

		$this->workerPhoto->file = $file->hashName();
		$this->workerPhoto->save();


		$this->actingAs($this->worker)->get($this->workerPhoto->url)->assertSuccessful();
	}

	public function test_admin_can_see_workers_decrypted_photo() {
        Crypt::shouldReceive('getKey')->twice()
            ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.jpg');
        $file->store('public/photos');
        $this->workerPhoto->file = $file->hashName();
        $this->workerPhoto->save();
		$this->actingAs($this->admin)->get($this->workerPhoto->url)->assertSuccessful();
	}

	public function test_guest_cant_see_band_member_photo() {
		$this->get($this->bandMemberPhoto->url)->assertRedirect(action('Auth\LoginController@showLoginForm'));
	}

	public function test_kitchen_cant_see_band_member_photo() {
		$this->actingAs($this->kitchen)->get($this->bandMemberPhoto->url)->assertForbidden();
	}

	public function test_accountant_cant_see_band_member_photo() {
		$this->actingAs($this->accountant)->get($this->bandMemberPhoto->url)->assertForbidden();
	}

	public function test_worker_cant_see_band_member_photo() {
		$this->actingAs($this->worker)->get($this->bandMemberPhoto->url)->assertForbidden();
	}

	public function test_other_band_member_cant_see_band_member_photo() {
		$bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => Band::factory()->create()->id
		])->user()->save($bandMember);

		$this->actingAs($bandMember)->get($this->bandMemberPhoto->url)->assertForbidden();
	}

	public function test_band_member_can_see_own_decrypted_photo() {
        Crypt::shouldReceive('getKey')->twice()
            ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.jpg');

        $file->store('public/photos');

		$this->bandMemberPhoto->file = $file->hashName();
		$this->bandMemberPhoto->save();


		$this->actingAs($this->bandMember)->get($this->bandMemberPhoto->url)->assertSuccessful();
	}

	public function test_admin_can_see_band_member_decrypted_photo() {
        Crypt::shouldReceive('getKey')->twice()
            ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.jpg');

        $file->store('public/photos');

		$this->bandMemberPhoto->file = $file->hashName();
		$this->bandMemberPhoto->save();


		$this->actingAs($this->admin)->get($this->bandMemberPhoto->url)->assertSuccessful();
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
		$worker = User::factory()->make();
		Worker::factory()->create()->user()->save($worker);

		$this->actingAs($worker)->get($this->taxReview->url)->assertForbidden();
	}

	public function test_worker_can_see_own_decrypted_tax_review() {
        Crypt::shouldReceive('getKey')->twice()
            ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.pdf');

        $file->store('public/taxReviews');

		$this->taxReview->file = $file->hashName();
		$this->taxReview->save();


		$this->actingAs($this->worker)->get($this->taxReview->url)->assertSuccessful();
	}

	public function test_admin_can_see_workers_decrypted_tax_review() {
        Crypt::shouldReceive('getKey')->twice()
            ->shouldReceive('encrypt')->times(2)->andReturn('')
            ->shouldReceive('decrypt')->once()
            ->with(Storage::get("public/photos/{$this->workerPhoto->file}"))->andReturn('');

        $file = UploadedFile::fake()->create('demo.pdf');

        $file->store('public/taxReviews');
		$this->taxReview->file = $file->hashName();
		$this->taxReview->save();
		$this->actingAs($this->worker)->get($this->taxReview->url)->assertSuccessful();
	}

}
