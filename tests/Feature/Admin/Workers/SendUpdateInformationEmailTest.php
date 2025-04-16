<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\Worker\UpdateInformationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class SendUpdateInformationEmailTest extends TestCase {
    use RefreshDatabase;

    protected $admin;
    protected $kitchen;
    protected $accountant;
    protected $worker;

    public function setUp(): void {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->admin->user()->save(User::factory()->make());
        $this->kitchen = Kitchen::factory()->create();
        $this->kitchen->user()->save(User::factory()->make());
        $this->accountant = User::factory()->make();
        Accountant::factory()->create()->user()->save($this->accountant);
        $this->worker = Worker::factory()->create();
        $this->worker->user()->save(User::factory()->make());


    }

    public function test_guest_cant_send_email() {
        $this->post(action('Admin\WorkerController@sendUpdateInformationEmail', $this->worker))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_worker_cant_send_email() {
        $this->actingAs($this->worker->user)->post(action('Admin\WorkerController@sendUpdateInformationEmail', $this->worker))->assertForbidden();
    }


    public function test_accountant_cant_send_email() {
        $this->actingAs($this->accountant)->post(action('Admin\WorkerController@sendUpdateInformationEmail', $this->worker))->assertForbidden();
    }


    public function test_kitchen_cant_send_email() {
        $this->actingAs($this->kitchen->user)->post(action('Admin\WorkerController@sendUpdateInformationEmail', $this->worker))->assertForbidden();
    }

    public function test_admin_can_send_update_email() {
        Notification::fake();
        $this->actingAs($this->admin->user)->post(action('Admin\WorkerController@sendUpdateInformationEmail', $this->worker))->assertSuccessful();
        Notification::assertSentTo($this->worker->user, UpdateInformationNotification::class);

    }
}
