<?php

namespace Tests\Feature\Admin\WorkedHoursExportColumn;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageTest extends TestCase {

	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $worker;

	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		$this->accountant = factory(User::class)->make();
		factory(Accountant::class)->create()->user()->save($this->accountant);
		$this->worker = factory(User::class)->make();
		factory(Worker::class)->create()->user()->save($this->worker);
	}


	public function test_guest_cant_see_page(){
		$this->get(action('Admin\WorkedHoursExportColumnController@show'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page(){
		$this->actingAs($this->kitchen)->get(action('Admin\WorkedHoursExportColumnController@show'))->assertForbidden();
	}

	public function test_accountant_cant_see_page(){
		$this->actingAs($this->accountant)->get(action('Admin\WorkedHoursExportColumnController@show'))->assertForbidden();
	}

	public function test_worker_cant_see_page(){
		$this->actingAs($this->worker)->get(action('Admin\WorkedHoursExportColumnController@show'))->assertForbidden();
	}

	public function test_admin_cant_see_page(){
		$this->actingAs($this->admin)->get(action('Admin\WorkedHoursExportColumnController@show'))->assertSuccessful();
	}
}
