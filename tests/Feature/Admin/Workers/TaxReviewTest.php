<?php

namespace Tests\Feature\Admin\Workers;

use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaxReviewTest extends TestCase {
	use RefreshDatabase;
	
	protected $admin;
	protected $kitchen;
	protected $worker;
	
	public function setUp() {
		parent::setUp();
		$this->admin = factory(Admin::class)->create();
		$this->admin->user()->save(factory(User::class)->make());
		$this->kitchen = factory(Kitchen::class)->create();
		$this->kitchen->user()->save(factory(User::class)->make());
		$this->worker = factory(Worker::class)->create();
		$this->worker->user()->save(factory(User::class)->make());
		
		
	}
	
	public function testExample() {
		$this->assertTrue(true);
	}
}
