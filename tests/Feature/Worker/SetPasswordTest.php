<?php

namespace Tests\Feature\Worker;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Carbon\Carbon;
use DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetPasswordTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	private $kitchenPhoto;
	private $workerPhoto;
	
	protected function setUp() {
		parent::setUp();
		
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		DB::table('password_resets')->insert(['email' => $this->worker->email, 'token' => bcrypt('111')]);
		
	}
	
	public function test_guest_can_access_set_password_page() {
		$this->get(action('Worker\WorkerController@showResetForm', '111'))->assertSuccessful();
	}
	
	public function test_kitchen_cant_access_set_password_page() {
		$this->actingAs($this->kitchen)->get(action('Worker\WorkerController@showResetForm', '111'))->assertRedirect($this->kitchen->user->homePage());
	}
	
	public function test_admin_cant_access_set_password_page() {
		$this->actingAs($this->admin)->get(action('Worker\WorkerController@showResetForm', '111'))->assertRedirect($this->admin->user->homePage());
	}
	
	public function test_worker_cant_access_set_password_page() {
		$this->actingAs($this->worker)->get(action('Worker\WorkerController@showResetForm', '111'))->assertRedirect($this->worker->user->homePage());
	}
	
	public function test_kitchen_cant_set_password_for_worker() {
		$this->actingAs($this->kitchen)->post(action('Worker\WorkerController@reset'))->assertRedirect($this->kitchen->user->homePage());
	}
	
	public function test_worker_cant_set_password_for_worker() {
		$this->actingAs($this->worker)->post(action('Worker\WorkerController@reset'))->assertRedirect($this->worker->user->homePage());
	}
	
	public function test_admin_cant_set_password_for_worker() {
		$this->actingAs($this->admin)->post(action('Worker\WorkerController@reset'))->assertRedirect($this->admin->user->homePage());
	}
	
	public function test_cant_set_password_with_wrong_token() {
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => 'bla',
			'email' => $this->worker->email,
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertSessionHasErrors(['email']);
	}
	
	public function test_cant_set_password_with_wrong_email() {
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => 'bla@gla.dla',
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertSessionHasErrors(['email']);
	}
	
	public function test_can_set_password_with_correct_credentials() {
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => $this->worker->email,
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertRedirect($this->worker->user->homepage());
		
		
		$this->assertAuthenticatedAs($this->worker);
		$this->assertEquals(0, DB::table('password_resets')->count());
	}
	
	public function test_can_set_password_with_correct_credentials_until_a_month_old_token() {
		DB::table('password_resets')->update(['created_at' => Carbon::now()->subMonth()->addDay()]);
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => $this->worker->email,
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertRedirect($this->worker->user->homepage());
		
		
		$this->assertAuthenticatedAs($this->worker);
		$this->assertEquals(0, DB::table('password_resets')->count());
	}
	
	public function test_cant_set_password_with_correct_credentials_with_older_than_a_month_token() {
		DB::table('password_resets')->update(['created_at' => Carbon::now()->subMonth()]);
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => $this->worker->email,
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertSessionHasErrors(['email']);
		
		
		$this->assertEquals(1, DB::table('password_resets')->count());
	}
	
	
	public function test_cant_set_password_with_bad_password() {
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => $this->worker->email,
			'password' => '123',
			'password_confirmation' => '123456',
		])->assertSessionHasErrors(['password']);
		
		
		$this->assertEquals(1, DB::table('password_resets')->count());
	}
	
	public function test_cant_set_password_with_inconfirmed_password() {
		$this->post(action('Worker\WorkerController@reset'), [
			'token' => '111',
			'email' => $this->worker->email,
			'password' => '123456',
			'password_confirmation' => '123',
		])->assertSessionHasErrors(['password']);
		
		
		$this->assertEquals(1, DB::table('password_resets')->count());
	}
}
