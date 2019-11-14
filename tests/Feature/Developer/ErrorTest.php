<?php

namespace Tests\Feature\Developer;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Developer;
use App\Models\Error;
use App\Models\JsError;
use App\Models\Kitchen;
use App\Models\PhpError;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;


	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $developer;
	protected $phpErrors;
	protected $jsErrors;
	protected $worker;

	protected function setUp(): void {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->developer = factory(Developer::class)->create();
		$this->developer->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->phpErrors = factory(PhpError::class,5)->create()->each(function ($phpError){
			$phpError->error()->save(factory(Error::class)->make());
		});
		$this->jsErrors = factory(JsError::class,5)->create()->each(function ($phpError){
			$phpError->error()->save(factory(Error::class)->make());
		});
	}

	public function test_guest_cant_see_php_error_page() {
		$this->get(action('Developer\ErrorController@phpErrors'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_php_error_page() {
		$this->actingAs($this->worker)->get(action('Developer\ErrorController@phpErrors'))->assertForbidden();
	}

	public function test_kitchen_cant_see_php_error_page() {
		$this->actingAs($this->kitchen->user)->get(action('Developer\ErrorController@phpErrors'))->assertForbidden();
	}

	public function test_accountant_cant_see_php_error_page() {
		$this->actingAs($this->accountant)->get(action('Developer\ErrorController@phpErrors'))->assertForbidden();
	}

	public function test_admin_cant_see_php_error_page() {
		$this->actingAs($this->admin->user)->get(action('Developer\ErrorController@phpErrors'))->assertForbidden();
	}

	public function test_developer_can_see_php_error_page() {
		$this->actingAs($this->developer->user)->get(action('Developer\ErrorController@phpErrors'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_guest_cant_see_js_error_page() {
		$this->get(action('Developer\ErrorController@jsErrors'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_js_error_page() {
		$this->actingAs($this->kitchen->user)->get(action('Developer\ErrorController@jsErrors'))->assertForbidden();
	}

	public function test_kitchen_cant_see_js_error_page() {
		$this->actingAs($this->kitchen->user)->get(action('Developer\ErrorController@jsErrors'))->assertForbidden();
	}

	public function test_accountant_cant_see_js_error_page() {
		$this->actingAs($this->accountant)->get(action('Developer\ErrorController@jsErrors'))->assertForbidden();
	}

	public function test_admin_cant_see_js_error_page() {
		$this->actingAs($this->admin->user)->get(action('Developer\ErrorController@jsErrors'))->assertForbidden();
	}

	public function test_developer_can_see_js_error_page() {
		$this->actingAs($this->developer->user)->get(action('Developer\ErrorController@jsErrors'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_php_error_datatable_gets_table_data() {
		$response = $this->actingAs($this->developer->user)->get(action('DatatableController@list', ['table' => 'developer.phpErrorsTable', 'per_page' => 20]));
		foreach ($this->phpErrors as $phpError) {
			$response->assertJsonFragment([
				'id' => "{$phpError->error->id}",
				'message' => $phpError->message,
				'page' => $phpError->error->page
			]);
		}
	}

	public function test_js_error_datatable_gets_table_data() {
		$response = $this->actingAs($this->developer->user)->get(action('DatatableController@list', ['table' => 'developer.jsErrorsTable', 'per_page' => 20]));
		foreach ($this->jsErrors as $jsError) {
			$response->assertJsonFragment([
				'id' => "{$jsError->error->id}",
				'message' => $jsError->message,
				'page' => $jsError->error->page
			]);
		}
	}

	public function test_guest_cant_resolve() {
		$this->delete(action('Developer\ErrorController@resolve', $this->phpErrors->first()->error))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_resolve() {
		$this->actingAs($this->worker)->delete(action('Developer\ErrorController@resolve',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_kitchen_cant_resolve() {
		$this->actingAs($this->kitchen->user)->delete(action('Developer\ErrorController@resolve',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_accountant_cant_resolve() {
		$this->actingAs($this->accountant)->delete(action('Developer\ErrorController@resolve',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_admin_cant_resolve() {
		$this->actingAs($this->admin->user)->delete(action('Developer\ErrorController@resolve',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_developer_can_resolve() {
		$this->actingAs($this->developer->user)->delete(action('Developer\ErrorController@resolve',$this->phpErrors->first()->error))->assertSuccessful();
		$this->assertDatabaseMissing('php_errors', ['id' => $this->phpErrors->first()->id]);
		$this->assertDatabaseMissing('errors', ['id' => $this->phpErrors->first()->error->id]);
	}

	public function test_guest_cant_get_full_data() {
		$this->get(action('Developer\ErrorController@show', $this->phpErrors->first()->error))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_get_full_data() {
		$this->actingAs($this->worker)->get(action('Developer\ErrorController@show',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_kitchen_cant_get_full_data() {
		$this->actingAs($this->kitchen->user)->get(action('Developer\ErrorController@show',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_accountant_cant_get_full_data() {
		$this->actingAs($this->accountant)->get(action('Developer\ErrorController@show',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_admin_cant_get_full_data() {
		$this->actingAs($this->admin->user)->get(action('Developer\ErrorController@show',$this->phpErrors->first()->error))->assertForbidden();
	}

	public function test_developer_can_get_full_data() {
		$response = $this->actingAs($this->developer->user)->get(action('Developer\ErrorController@show',$this->phpErrors->first()->error))->assertSuccessful();
		$response->assertJson($this->phpErrors->first()->error->fullData->toArray());
	}

	public function test_store_js_error(){
		$this->actingAs($this->kitchen->user)->post(action('Developer\ErrorController@storeJsError'), [
			'page' => 'test.com',
			'message' => 'test',
			'source' => 'test',
			'line_number' => 'test',
			'trace' => 'test',
			'userAgent' => 'test',
			'vm' => 'vm'
		] ,array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$this->assertDatabaseHas('errors', ['user_id' => $this->kitchen->user->id]);
		$this->assertDatabaseHas('js_errors', ['message' => 'test']);
	}
}
