<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CheckInfoTest extends TestCase {
    protected $user;
    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cant_check_info() {
        $this->post(action('UserController@checkInfo'))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_user_can_check_info() {
        $this->actingAs($this->user)->post(action('UserController@checkInfo'))->assertSuccessful();
        $this->assertTrue($this->user->checked_info);
    }
}
