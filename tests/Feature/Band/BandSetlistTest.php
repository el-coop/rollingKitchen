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
use App\Models\SetListFile;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BandSetlistTest extends TestCase {
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
		factory(Band::class)->create()->user()->save($this->band);
		$this->bandMember = factory(User::class)->make();
		factory(BandMember::class)->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_upload_band_setlist() {
		$this->post(action('Band\BandController@uploadSetlist', $this->band->user))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_upload_band_setlist() {
		$this->actingAs($this->worker)->post(action('Band\BandController@uploadSetlist', $this->band->user))->assertForbidden();

	}

	public function test_accountant_cant_upload_band_setlist() {
		$this->actingAs($this->accountant)->post(action('Band\BandController@uploadSetlist', $this->band->user))->assertForbidden();

	}

	public function test_kitchen_cant_upload_band_setlist() {
		$this->actingAs($this->kitchen)->post(action('Band\BandController@uploadSetlist', $this->band->user))->assertForbidden();

	}

	public function test_band_member_cant_upload_band_setlist() {
		$this->actingAs($this->bandMember)->post(action('Band\BandController@uploadSetlist', $this->band->user))->assertForbidden();

	}

	public function test_artist_manager_can_upload_band_setlist() {
		Storage::fake('local');
		$setlist = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->artistManager)->post(action('Band\BandController@uploadSetlist', $this->band->user), [
			'file' => $setlist,
            'owned' => 'yes',
            'protected' => 'no'
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('set_list_files', [
			'band_id' => $this->band->user->id
		]);
        $setlist = SetListFile::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $setlist->file);
        $this->assertEquals('yes',$setlist->owned);
        $this->assertEquals('no',$setlist->protected);

	}

	public function test_admin_can_upload_band_setlist() {

		Storage::fake('local');
        $setlist = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->admin)->post(action('Band\BandController@uploadSetlist', $this->band->user), [
			'file' => $setlist,
            'owned' => 'yes',
            'protected' => 'no'
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('set_list_files', [
			'band_id' => $this->band->user->id,
		]);
        $setlist = SetListFile::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $setlist->file);
        $this->assertEquals('yes',$setlist->owned);
        $this->assertEquals('no',$setlist->protected);

	}

	public function test_band_can_upload_band_setlist() {
		Storage::fake('local');
		$setlist = UploadedFile::fake()->create('test.pdf');
		$this->actingAs($this->band)->post(action('Band\BandController@uploadSetlist', $this->band->user), [
			'file' => $setlist,
            'owned' => 'yes',
            'protected' => 'no'
		])->assertRedirect()->assertSessionHas('toast');
		$this->assertDatabaseHas('set_list_files', [
			'band_id' => $this->band->user->id
		]);
        $setlist = SetListFile::first();
		Storage::disk('local')->assertExists('public/pdf/band/' . $setlist->file);
        $this->assertEquals('yes',$setlist->owned);
        $this->assertEquals('no',$setlist->protected);

	}

	public function test_upload_replaces_file() {
		Storage::fake('local');
        $setlist = UploadedFile::fake()->create('test.pdf');
		$path = $setlist->store('public/pdf/band');
		$oldSetlist = factory(SetListFile::class)->create([
			'file' => basename($path),
			'band_id' => $this->band->user->id,
            'owned' => 'no',
            'protected' => 'Yes'
		]);
		$newSetlist = UploadedFile::fake()->create('new.pdf');
		$this->actingAs($this->band)->post(action('Band\BandController@uploadSetlist', $this->band->user), [
			'file' => $newSetlist,
            'owned' => 'yes',
            'protected' => 'no'
		])->assertRedirect()->assertSessionHas('toast');
        $newSetlist = $this->band->user->setlistFile;
		Storage::disk('local')->assertMissing('public/pdf/band/' . $oldSetlist->file);
		Storage::disk('local')->assertExists('public/pdf/band/' . $newSetlist->file);
		$this->assertDatabaseHas('set_list_files', [
			'band_id' => $this->band->user->id,
            'owned' => 'yes',
            'protected' => 'no'
		]);

	}
}
