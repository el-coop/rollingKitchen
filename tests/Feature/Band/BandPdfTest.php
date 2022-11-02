<?php

namespace Tests\Feature\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandPdf;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BandPdfTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
	protected $bandAdmin;
	protected $bandMember;

	protected function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->artistManager = User::factory()->make();
		ArtistManager::factory()->create()->user()->save($this->artistManager);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->band = User::factory()->make();
		Band::factory()->create()->user()->save($this->band);
		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_upload_band_pdf() {
		$this->post(action('Band\BandController@uploadFile', $this->band->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_upload_band_pdf() {
		$this->actingAs($this->worker)->post(action('Band\BandController@uploadFile', $this->band->user))->assertForbidden();

	}

	public function test_accountant_cant_upload_band_pdf() {
		$this->actingAs($this->accountant)->post(action('Band\BandController@uploadFile', $this->band->user))->assertForbidden();

	}

	public function test_kitchen_cant_upload_band_pdf() {
		$this->actingAs($this->kitchen)->post(action('Band\BandController@uploadFile', $this->band->user))->assertForbidden();

	}

	public function test_band_member_cant_upload_band_pdf() {
		$this->actingAs($this->bandMember)->post(action('Band\BandController@uploadFile', $this->band->user))->assertForbidden();

	}

	public function test_artist_manager_can_upload_band_pdf() {
		Storage::fake('local');
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->artistManager)->post(action('Band\BandController@uploadFile', $this->band->user), [
			'file' => $pdf
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('band_pdfs', [
			'band_id' => $this->band->user->id
		]);
		$pdf = BandPdf::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $pdf->file);

	}

	public function test_admin_can_upload_band_pdf() {

		Storage::fake('local');
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->admin)->post(action('Band\BandController@uploadFile', $this->band->user), [
			'file' => $pdf
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('band_pdfs', [
			'band_id' => $this->band->user->id
		]);
		$pdf = BandPdf::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $pdf->file);

	}

	public function test_band_can_upload_band_pdf() {
		Storage::fake('local');
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->band)->post(action('Band\BandController@uploadFile', $this->band->user), [
			'file' => $pdf
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('band_pdfs', [
			'band_id' => $this->band->user->id
		]);
		$pdf = BandPdf::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $pdf->file);

	}

	public function test_upload_replaces_file() {
		Storage::fake('local');
		$pdf = UploadedFile::fake()->create('test.pdf');
		$path = $pdf->store('public/pdf/band');
		$oldPdf = BandPdf::factory()->create([
			'file' => basename($path),
			'band_id' => $this->band->user->id
		]);
		$newPdf = UploadedFile::fake()->create('new.pdf');
		$this->actingAs($this->band)->post(action('Band\BandController@uploadFile', $this->band->user), [
			'file' => $newPdf
		])->assertRedirect()->assertSessionHas('toast');
		$newPdf = $this->band->user->pdf;
		Storage::disk('local')->assertMissing('public/pdf/band/' . $oldPdf->file);
		Storage::disk('local')->assertExists('public/pdf/band/' . $newPdf->file);
		$this->assertDatabaseHas('band_pdfs', [
			'band_id' => $this->band->user->id
		]);

	}
}
