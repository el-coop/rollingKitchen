<?php

namespace Tests\Feature\Band;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetPasswordTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $worker;
	protected $artistManager;
	protected $accountant;
	protected $band;
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
		DB::table('password_resets')->insert(['email' => $this->band->email, 'token' => bcrypt('111')]);

	}

	public function test_guest_can_access_set_password_page() {
		$this->get(action('Band\BandController@showResetForm', '111'))->assertSuccessful();
	}

	public function test_kitchen_cant_access_set_password_page() {
		$this->actingAs($this->kitchen)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect($this->kitchen->user->homePage());
	}

	public function test_accountant_cant_access_set_password_page() {
		$this->actingAs($this->accountant)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect(action('HomeController@show'));
	}

	public function test_band_cant_access_set_password_page() {
		$this->actingAs($this->band)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect($this->band->user->homePage());
	}

	public function test_admin_cant_access_set_password_page() {
		$this->actingAs($this->admin)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect($this->admin->user->homePage());
	}

	public function test_worker_cant_access_set_password_page() {
		$this->actingAs($this->worker)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect($this->worker->user->homePage());
	}

	public function test_artist_manager_cant_access_set_password_page() {
		$this->actingAs($this->artistManager)->get(action('Band\BandController@showResetForm', '111'))->assertRedirect($this->artistManager->user->homePage());
	}

	public function test_kitchen_cant_set_password_for_band() {
		$this->actingAs($this->kitchen)->post(action('Band\BandController@reset'))->assertRedirect($this->kitchen->user->homePage());
	}

	public function test_worker_cant_set_password_for_band() {
		$this->actingAs($this->worker)->post(action('Band\BandController@reset'))->assertRedirect($this->worker->user->homePage());
	}

	public function test_accountant_cant_set_password_for_band() {
		$this->actingAs($this->accountant)->post(action('Band\BandController@reset'))->assertRedirect(action('HomeController@show'));
	}

	public function test_admin_cant_set_password_for_band() {
		$this->actingAs($this->admin)->post(action('Band\BandController@reset'))->assertRedirect($this->admin->user->homePage());
	}

	public function test_band_cant_set_password_for_band(){
		$this->actingAs($this->band)->post(action('Band\BandController@reset'))->assertRedirect($this->band->user->homePage());
	}

	public function test_artist_manager_cant_set_password_for_band() {
		$this->actingAs($this->artistManager)->post(action('Band\BandController@reset'))->assertRedirect($this->artistManager->user->homePage());
	}

	public function test_cant_set_password_with_wrong_token() {
		$this->post(action('Band\BandController@reset'), [
			'token' => 'bla',
			'email' => $this->band->email,
			'password' => '12345678',
			'password_confirmation' => '12345678',
		])->assertSessionHasErrors(['email']);
	}

	public function test_cant_set_password_with_wrong_email() {
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => 'bla@gla.dla',
			'password' => '12345678',
			'password_confirmation' => '12345678',
		])->assertSessionHasErrors(['email']);
	}

	public function test_can_set_password_with_correct_credentials() {
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => $this->band->email,
			'password' => '12345678',
			'password_confirmation' => '12345678',
		])->assertRedirect($this->band->user->homepage());


		$this->assertAuthenticatedAs($this->band);
		$this->assertEquals(0, DB::table('password_resets')->count());
	}

	public function test_can_set_password_with_correct_credentials_until_a_month_old_token() {
		DB::table('password_resets')->update(['created_at' => Carbon::now()->subMonth()->addDay()]);
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => $this->band->email,
			'password' => '12345678',
			'password_confirmation' => '12345678',
		])->assertRedirect($this->band->user->homepage());


		$this->assertAuthenticatedAs($this->band);
		$this->assertEquals(0, DB::table('password_resets')->count());
	}

	public function test_cant_set_password_with_correct_credentials_with_older_than_a_month_token() {
		DB::table('password_resets')->update(['created_at' => Carbon::now()->subDays(31)]);
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => $this->band->email,
			'password' => '12345678',
			'password_confirmation' => '12345678',
		])->assertSessionHasErrors(['email']);


		$this->assertEquals(1, DB::table('password_resets')->count());
	}


	public function test_cant_set_password_with_bad_password() {
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => $this->band->email,
			'password' => '123',
			'password_confirmation' => '123456',
		])->assertSessionHasErrors(['password']);


		$this->assertEquals(1, DB::table('password_resets')->count());
	}

	public function test_cant_set_password_with_inconfirmed_password() {
		$this->post(action('Band\BandController@reset'), [
			'token' => '111',
			'email' => $this->band->email,
			'password' => '123456',
			'password_confirmation' => '123',
		])->assertSessionHasErrors(['password']);


		$this->assertEquals(1, DB::table('password_resets')->count());
	}
}
