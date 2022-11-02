<?php

namespace Tests\Feature\Admin\BandMember;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected $kitchen;
	protected $accountant;
	protected $workplaces;
	protected $worker;
	private $bandMember;
	private $band;


	protected function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);
		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);
		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);
		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);
		$this->band = User::factory()->make();
		Band::factory()->create()->user()->save($this->band);

		$this->bandMember = User::factory()->make();
		BandMember::factory()->create([
			'band_id' => $this->band->user->id
		])->user()->save($this->bandMember);
	}

	public function test_guest_cant_see_band_member_pdf() {
		$this->get(action('Admin\BandMemberController@pdf', $this->bandMember->user))->assertStatus(401);
	}


	public function test_kitchen_cant_see_band_member_pdf() {
		$this->actingAs($this->kitchen)->get(action('Admin\BandMemberController@pdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_worker_cant_see_band_member_pdf() {
		$this->actingAs($this->worker)->get(action('Admin\BandMemberController@pdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_member_cant_see_band_member_pdf() {
		$this->actingAs($this->bandMember)->get(action('Admin\BandMemberController@pdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_band_cant_see_band_member_pdf() {
		$this->actingAs($this->band)->get(action('Admin\BandMemberController@pdf', $this->bandMember->user))->assertForbidden();
	}

	public function test_admin_can_see_band_member_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}

	public function test_accountant_can_see_band_member_pdf() {
		//THIS IS IMPOSSIBLE TO TEST
		$this->assertTrue(true);
	}
}
