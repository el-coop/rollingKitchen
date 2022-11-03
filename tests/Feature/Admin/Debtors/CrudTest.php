<?php

namespace Admin\Kitchens\Debtors;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Debtor;
use App\Models\Developer;
use App\Models\Kitchen;
use App\Models\User;
use App\Models\Worker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrudTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;

	protected $worker;
	private $accountant;
	private $admin;
	private $debtors;
	private $developer;
	private $kitchen;

	public function setUp(): void {
		parent::setUp();
		$this->admin = User::factory()->make();
		Admin::factory()->create()->user()->save($this->admin);

		$this->worker = User::factory()->make();
		Worker::factory()->create()->user()->save($this->worker);

		$this->kitchen = User::factory()->make();
		Kitchen::factory()->create()->user()->save($this->kitchen);

		$this->accountant = User::factory()->make();
		Accountant::factory()->create()->user()->save($this->accountant);

		$this->debtors = Debtor::factory( 5)->create();

		$this->developer = User::factory()->make();
		Developer::factory()->create()->user()->save($this->developer);
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\DebtorController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_see_page() {
		$this->actingAs($this->worker)->get(action('Admin\DebtorController@index'))->assertForbidden();
	}


	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\DebtorController@index'))->assertForbidden();
	}

	public function test_accountant_cant_see_page() {
		$this->actingAs($this->accountant)->get(action('Admin\DebtorController@index'))->assertForbidden();
	}

	public function test_page_loads_with_datatable() {
		$this->actingAs($this->admin)->get(action('Admin\DebtorController@index'))
			->assertStatus(200)
			->assertSee('</datatable>', false);
	}

	public function test_page_loads_with_datatable_developer() {
		$this->actingAs($this->developer)->get(action('Admin\DebtorController@index'))
			->assertStatus(200)
			->assertSee('</datatable>', false);
	}

	public function test_datatable_get_table_data_sorted() {
		$response = $this->actingAs($this->admin)->get(action('DatatableController@list', ['table' => 'admin.debtorsTable', 'per_page' => 20, 'sort' => 'name|asc']));

		$debtors = array_values($this->debtors->map(function ($debtor) {
			return [
				'id' => $debtor->id,
				'name' => $debtor->name,
				'email' => $debtor->email,
			];
		})->sortBy('name')->toArray());

		foreach ($response->json()['data'] as $key => $responseFragment) {
			$this->assertEquals($responseFragment['id'], $debtors[$key]['id']);
		}
	}

	public function test_datatable_get_table_data_filtered() {
		$this->debtors->push(Debtor::factory()->create([
			'name' => 'bla'
		]));
		$response = $this->actingAs($this->admin)
			->get(action('DatatableController@list', ['table' => 'admin.debtorsTable', 'per_page' => 20, 'filter' => '{"name":"bla"}']));

		$debtors = $this->debtors->filter(function ($debtor) {
			return $debtor->name == 'bla';
		});

		foreach ($debtors as $debtor) {
			$response->assertJsonFragment([
				'id' => $debtor->id,
				'name' => $debtor->name,
				'email' => $debtor->email,
			]);
		}
	}

	public function test_guest_cant_edit_debtor() {
		$debtor = $this->debtors->random();
		$this->patch(action('Admin\DebtorController@update', $debtor))->assertRedirect(action('Auth\LoginController@login'));
	}


	public function test_worker_cant_edit_debtor() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->worker)->patch(action('Admin\DebtorController@update', $debtor))->assertForbidden();
	}

	public function test_accountant_cant_edit_debtor() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->accountant)->patch(action('Admin\DebtorController@update', $debtor))->assertForbidden();
	}

	public function test_kitchen_cant_edit_debtor() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->kitchen)->patch(action('Admin\DebtorController@update', $debtor))->assertForbidden();
	}

	public function test_guest_cant_get_debtor_fields() {
		$debtor = $this->debtors->random();

		$this->get(action('Admin\DebtorController@edit', $debtor))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_worker_cant_get_debtor_fields() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->worker)->get(action('Admin\DebtorController@edit', $debtor))->assertForbidden();
	}

	public function test_accountant_cant_get_debtor_fields() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->accountant)->get(action('Admin\DebtorController@edit', $debtor))->assertForbidden();
	}

	public function test_kitchen_cant_get_debtor_fields() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->kitchen)->get(action('Admin\DebtorController@edit', $debtor))->assertForbidden();
	}

	public function test_admin_can_update_debtor() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->admin)
			->patch(action('Admin\DebtorController@update', $debtor), [
				'name' => 'testname',
				'email' => 'test@emial.com',
				'language' => 'nl',
				'kitchen' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				]
			])->assertSuccessful();

		$this->assertDatabaseHas('debtors', [
			'id' => $debtor->id,
			'name' => 'testname',
			'email' => 'test@emial.com',
			'language' => 'nl'
			]);
		$updatedDebtor = Debtor::find($debtor->id);
        $this->assertEquals(collect([
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
        ]), $updatedDebtor->data);
    }

	public function test_update_debtor_validates() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->admin)
			->patch(action('Admin\DebtorController@update', $debtor), [
				'name' => '',
				'email' => 'test',
				'language' => 'l',
				'kitchen' => 'test'
			])->assertRedirect()->assertSessionHasErrors(['name', 'email', 'language', 'kitchen']);
	}

	public function test_admin_can_create_debtor() {

		$this->actingAs($this->admin)
			->post(action('Admin\DebtorController@store'), [
				'name' => 'testname',
				'email' => 'test@emial.com',
				'language' => 'nl',
				'kitchen' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				]
			])->assertSuccessful();

		$this->assertDatabaseHas('debtors', [
			'name' => 'testname',
			'email' => 'test@emial.com',
			'language' => 'nl',

		]);
        $debtor = Debtor::where(['name' => 'testname'])->first();
        $this->assertEquals(collect([
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
        ]), $debtor->data);
	}

	public function test_guest_cant_create_debtor() {

		$this->post(action('Admin\DebtorController@store'), [
			'name' => 'testname',
			'email' => 'test@emial.com',
			'language' => 'nl',
			'kitchen' => [
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
			]
		])->assertRedirect(action('Auth\LoginController@login'));

	}

	public function test_worker_cant_create_debtor() {

		$this->actingAs($this->worker)
			->post(action('Admin\DebtorController@store'), [
				'name' => 'testname',
				'email' => 'test@emial.com',
				'language' => 'nl',
				'kitchen' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				]
			])->assertForbidden();

	}

	public function test_accountant_cant_create_debtor() {

		$this->actingAs($this->accountant)
			->post(action('Admin\DebtorController@store'), [
				'name' => 'testname',
				'email' => 'test@emial.com',
				'language' => 'nl',
				'kitchen' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				]
			])->assertForbidden();

	}
	public function test_kitchen_cant_create_debtor() {

		$this->actingAs($this->kitchen)
			->post(action('Admin\DebtorController@store'), [
				'name' => 'testname',
				'email' => 'test@emial.com',
				'language' => 'nl',
				'kitchen' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				]
			])->assertForbidden();

	}

	public function test_create_debtor_validates() {

		$this->actingAs($this->admin)
			->post(action('Admin\DebtorController@store'), [
				'name' => '',
				'email' => 'test',
				'language' => 'l',
				'kitchen' => 'test'
			])->assertRedirect()->assertSessionHasErrors(['name', 'email', 'language', 'kitchen']);
	}

	public function test_guest_cant_delete_debtor() {
		$this->delete(action('Admin\DebtorController@destroy', $this->debtors->first()))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_delete_debtor() {
		$this->actingAs($this->kitchen)->delete(action('Admin\DebtorController@destroy', $this->debtors->first()))->assertForbidden();
	}

	public function test_accountant_cant_delete_debtor() {
		$this->actingAs($this->accountant)->delete(action('Admin\DebtorController@destroy', $this->debtors->first()))->assertForbidden();
	}

	public function test_worker_cant_delete_debtor() {
		$this->actingAs($this->worker)->delete(action('Admin\DebtorController@destroy', $this->debtors->first()))->assertForbidden();
	}

	public function test_admin_can_delete_debtor() {
		$debtor = $this->debtors->first();
		$this->actingAs($this->admin)->delete(action('Admin\DebtorController@destroy', $debtor))->assertSuccessful();
		$this->assertDatabaseMissing('debtors', ['id' => $debtor->id]);

	}

	public function test_developer_can_delete_debtor() {
		$debtor = $this->debtors->first();
		$this->actingAs($this->developer)->delete(action('Admin\DebtorController@destroy', $debtor))->assertSuccessful();
		$this->assertDatabaseMissing('debtors', ['id' => $debtor->id]);

	}


}
