<?php

namespace Tests\Feature\Admin\Settings;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogoTest extends TestCase {
    use RefreshDatabase;
    use WithFaker;

    protected $admin;
    protected $kitchen;
    protected $accountant;
    protected $worker;

    public function setUp(): void {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->admin->user()->save(User::factory()->make());
        $this->worker = User::factory()->make();
        Worker::factory()->create()->user()->save($this->worker);
        $this->accountant = User::factory()->make();
        Accountant::factory()->create()->user()->save($this->accountant);
        $this->kitchen = Kitchen::factory()->create();
        $this->kitchen->user()->save(User::factory()->make());
        Storage::fake('local');
    }

    public function test_guest_cant_upload_logo() {
        $logo = UploadedFile::fake()->image('test.png');
        $this->post(action('Admin\LogoController@store'), [ 'photo' => $logo])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_worker_cant_upload_logo() {
        $logo = UploadedFile::fake()->image('test.png');
        $this->actingAs($this->worker)->post(action('Admin\LogoController@store'), [ 'photo' => $logo])->assertForbidden();
    }

    public function test_kitchen_cant_upload_logo() {
        $logo = UploadedFile::fake()->image('test.png');
        $this->actingAs($this->kitchen->user)->post(action('Admin\LogoController@store'), [ 'photo' => $logo])->assertForbidden();
    }

    public function test_accountant_cant_upload_logo() {
        $logo = UploadedFile::fake()->image('test.png');
        $this->actingAs($this->accountant)->post(action('Admin\LogoController@store'), [ 'photo' => $logo])->assertForbidden();
    }

    public function test_admin_can_upload_logo() {
        $logo = UploadedFile::fake()->image('test.png');
        $this->actingAs($this->admin->user)->post(action('Admin\LogoController@store'), [
            'photo' => $logo,
        ])->assertSuccessful();
        Storage::disk('local')->assertExists('public/images/logo.png');
    }

    public function test_upload_logo_validation() {
        $this->actingAs($this->admin->user)->post(action('Admin\LogoController@store'), [])
            ->assertRedirect()->assertSessionHasErrors( 'photo');
    }
}
