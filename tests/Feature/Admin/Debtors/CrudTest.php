<?php

namespace Tests\Feature\Admin\Debtors;

use App\Models\Admin;
use App\Models\Debtor;
use App\Models\Developer;
use App\Models\Kitchen;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrudTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;

	private $admin;
	private $debtors;
	private $developer;
	private $kitchen;

	public function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);


		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);

		$this->debtors = factory(Debtor::class, 5)->create();

		$this->developer = factory(User::class)->make();
		factory(Developer::class)->create()->user()->save($this->developer);
	}

	public function test_guest_cant_see_page() {
		$this->get(action('Admin\DebtorController@index'))->assertRedirect(action('Auth\LoginController@login'));
	}

	public function test_kitchen_cant_see_page() {
		$this->actingAs($this->kitchen)->get(action('Admin\DebtorController@index'))->assertForbidden();
	}

	public function test_page_loads_with_datatable() {
		$this->actingAs($this->admin)->get(action('Admin\DebtorController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
	}

	public function test_page_loads_with_datatable_developer() {
		$this->actingAs($this->developer)->get(action('Admin\DebtorController@index'))
			->assertStatus(200)
			->assertSee('</datatable>');
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
		$this->debtors->push(factory(Debtor::class)->create([
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

	public function test_kitchen_cant_edit_debtor() {
		$debtor = $this->debtors->random();

		$this->actingAs($this->kitchen)->patch(action('Admin\DebtorController@update', $debtor))->assertForbidden();
	}

	public function test_guest_cant_get_debtor_fields() {
		$debtor = $this->debtors->random();

		$this->get(action('Admin\DebtorController@edit', $debtor))->assertRedirect(action('Auth\LoginController@login'));
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
			'language' => 'nl',
			'data' => json_encode([
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
			])

		]);
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
			'data' => json_encode([
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
			])
		]);
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
