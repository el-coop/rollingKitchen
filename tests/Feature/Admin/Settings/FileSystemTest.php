<?php

namespace Tests\Feature\Admin\Settings;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileSystemTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $pdf;
	protected $worker;

	public function setUp(): void {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		Storage::fake('local');
		$pdf = UploadedFile::fake()->create('first.pdf');
		$path = $pdf->store('public/pdf');
		$this->pdf = factory(Pdf::class)->create([
			'name' => 'first',
			'visibility' => 1,
			'file' => basename($path),
		]);


	}
	public function test_guest_cant_see_page() {
		$this->get(action('Admin\PDFController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}
	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\PDFController@index'))->assertForbidden();
	}
	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen->user)->get(action('Admin\PDFController@index'))->assertForbidden();
	}
	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\PDFController@index'))->assertForbidden();
	}

	public function test_admin_can_see_page() {
		$this->actingAs($this->admin->user)->get(action('Admin\PDFController@index'))
			->assertStatus(200)
			->assertSee(__('admin/settings.files'));
	}

	public function test_guest_cant_upload_pdf() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->post(action('Admin\PDFController@upload'), ['name' => 'newPDF', 'file' => $pdf])->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_upload_pdf() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->worker)->post(action('Admin\PDFController@upload'), ['name' => 'newPDF', 'file' => $pdf])->assertForbidden();
	}

	public function test_kitchen_cant_upload_pdf() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->kitchen->user)->post(action('Admin\PDFController@upload'), ['name' => 'newPDF', 'file' => $pdf])->assertForbidden();
	}

	public function test_accountant_cant_upload_pdf() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->accountant)->post(action('Admin\PDFController@upload'), ['name' => 'newPDF', 'file' => $pdf])->assertForbidden();
	}

	public function test_admin_can_upload_pdf() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->admin->user)->post(action('Admin\PDFController@upload'), [
			'name' => 'newPDF',
			'visibility' => 0,
			'file' => $pdf,
			'default_send_invoice' => true
		])->assertSuccessful();
		$this->assertDatabaseHas('pdfs', [
			'visibility' => 0,
			'name' => 'newPDF',
		]);
		$pdf = Pdf::where('name', 'newPDF')->first();
		$this->assertTrue($pdf->default_send_invoice);
		$this->assertFalse($pdf->default_resend_invoice);
		Storage::disk('local')->assertExists('public/pdf/' . $pdf->file);
	}

	public function test_upload_pdf_validation() {
		$this->actingAs($this->admin->user)->post(action('Admin\PDFController@upload'), [])
			->assertRedirect()->assertSessionHasErrors(['visibility', 'name', 'file']);
	}

	public function test_admin_can_update_pdf() {
		$this->actingAs($this->admin->user)->patch(action('Admin\PDFController@update', $this->pdf), [
			'name' => 'test',
			'visibility' => 2,
			'default_resend_invoice' => true,
			'default_send_invoice' => true
			])->assertSuccessful();
		$this->assertDatabaseHas('pdfs', [
			'visibility' => 2,
			'name' => 'test',
			'default_resend_invoice' => true,
			'default_send_invoice' => true
		]);
	}

	public function test_admin_can_update_pdf_keeping_same_name(){
		$this->actingAs($this->admin->user)->patch(action('Admin\PDFController@update', $this->pdf), ['name' => $this->pdf->name, 'visibility' => 2])->assertSuccessful();
		$this->assertDatabaseHas('pdfs', [
			'visibility' => 2,
			'name' => 'first',
		]);
	}

	public function test_update_pdf_validation() {
		$this->actingAs($this->admin->user)->patch(action('Admin\PDFController@update', $this->pdf), [])
			->assertRedirect()->assertSessionHasErrors(['visibility', 'name']);
	}

	public function test_guest_cant_destroy_pdf() {
		$this->delete(action('Admin\PDFController@destroy', $this->pdf))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_destroy_pdf() {
		$this->actingAs($this->worker)->delete(action('Admin\PDFController@destroy', $this->pdf))->assertForbidden();
	}

	public function test_kitchen_cant_destroy_pdf() {
		$this->actingAs($this->kitchen->user)->delete(action('Admin\PDFController@destroy', $this->pdf))->assertForbidden();
	}

	public function test_accountant_cant_destroy_pdf() {
		$this->actingAs($this->accountant)->delete(action('Admin\PDFController@destroy', $this->pdf))->assertForbidden();
	}

	public function test_admin_can_destroy_pdf() {
		$path = $this->pdf->file;
		$this->actingAs($this->admin->user)->delete(action('Admin\PDFController@destroy', $this->pdf))->assertSuccessful();
		$this->assertDatabaseMissing('pdfs', ['name' => 'first']);
		Storage::disk('local')->assertMissing($path);
	}

	public function test_default_attach_returns_true_when_request_has() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->admin->user)->post(action('Admin\PDFController@upload'), [
			'name' => 'newPDF',
			'visibility' => 0,
			'file' => $pdf,
			'default_send_invoice' => true,
			'default_resend_invoice' => true
		])->assertSuccessful();
		$this->assertDatabaseHas('pdfs', [
			'visibility' => 0,
			'name' => 'newPDF',
		]);
		$pdf = Pdf::where('name', 'newPDF')->first();
		$this->assertTrue($pdf->default_send_invoice);
		$this->assertTrue($pdf->default_resend_invoice);
		Storage::disk('local')->assertExists('public/pdf/' . $pdf->file);
	}

	public function test_default_attach_returns_false_when_absent_from_request() {
		$pdf = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->admin->user)->post(action('Admin\PDFController@upload'), [
			'name' => 'newPDF',
			'visibility' => 0,
			'file' => $pdf,
		])->assertSuccessful();
		$this->assertDatabaseHas('pdfs', [
			'visibility' => 0,
			'name' => 'newPDF',
		]);
		$pdf = Pdf::where('name', 'newPDF')->first();
		$this->assertFalse($pdf->default_send_invoice);
		$this->assertFalse($pdf->default_resend_invoice);
		Storage::disk('local')->assertExists('public/pdf/' . $pdf->file);
	}
}
