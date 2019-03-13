<?php

namespace Tests\Feature\Admin;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KitchenViewTest extends TestCase {
	use RefreshDatabase;
	
	protected $worker;
	private $admin;
	private $accountant;
	private $kitchen;
	private $application;
	private $product;
	
	protected function setUp() {
		parent::setUp();
		$admin = factory(Admin::class)->create();
		$admin->user()->save(factory(User::class)->make());
		$this->admin = $admin->user;
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->application = factory(Application::class)->make();
		$this->kitchen->applications()->save($this->application);
		$this->product = factory(Product::class)->make();
		$this->application->products()->save($this->product);
		
	}
	
	public function test_guest_cant_view_kitchen_and_application_test() {
		$this->get(action('Admin\KitchenController@show', $this->kitchen))
			->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_view_kitchen_and_application_test() {
		$this->actingAs($this->worker)->get(action('Admin\KitchenController@show', $this->kitchen))
			->assertForbidden();
	}

	public function test_accountant_cant_view_kitchen_and_application_test() {
		$this->actingAs($this->accountant)->get(action('Admin\KitchenController@show', $this->kitchen))
			->assertForbidden();
	}

	public function test_kitchen_cant_view_kitchen_and_application_test() {
		$this->actingAs($this->kitchen->user)->get(action('Admin\KitchenController@show', $this->kitchen))
			->assertForbidden();
	}
	
	public function test_admin_can_view_kitchen_and_application_test() {
		$this->actingAs($this->admin)->get(action('Admin\KitchenController@show', $this->kitchen))
			->assertSuccessful()
			->assertViewHas('kitchen', $this->kitchen);
	}
}
