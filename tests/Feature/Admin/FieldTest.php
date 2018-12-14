<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Application;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FieldTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	protected  $kitchen;
	protected $fields;

	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
		$this->kitchen = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->kitchen);
		factory(Field::class,5)->create();
		$this->fields = Field::all();

    }

	public function test_can_see_Kitchen_field_list() {
		$this->actingAs($this->admin)->get(action('Admin\FieldController@index', 'Kitchen'))->assertSuccessful()->assertViewIs('admin.fields');
	}

	public function test_can_see_application_field_list() {
		$this->actingAs($this->admin)->get(action('Admin\FieldController@index', 'Application'))->assertSuccessful()->assertViewIs('admin.fields');
	}

    public function test_guest_cant_see_any_field_list(){
        $this->get(action('Admin\FieldController@index', 'Kitchen'))->assertRedirect(action('Auth\LoginController@login'));
        $this->get(action('Admin\FieldController@index', 'Application'))->assertRedirect(action('Auth\LoginController@login'));

    }

    public function test_kitchen_cant_see_any_field_list(){
        $this->actingAs($this->kitchen)->get(action('Admin\FieldController@index', 'Kitchen'))->assertForbidden();
        $this->actingAs($this->kitchen)->get(action('Admin\FieldController@index', 'Application'))->assertForbidden();

    }

	public function test_admin_can_create_field() {
		$this->actingAs($this->admin)->post(action('Admin\FieldController@create'), [
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
			'status' => 'required'
		])->assertSuccessful()->assertJson([
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
			'status' => 'required'
		]);
		$this->assertDatabaseHas('fields', [
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
		]);
	}

    public function test_guest_cant_create_field() {
        $this->post(action('Admin\FieldController@create'), [
            'name_en' => 'test',
            'name_nl' => 'test',
            'type' => 'text',
            'form' => Application::class,
        ])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_create_field() {
        $this->actingAs($this->kitchen)->post(action('Admin\FieldController@create'), [
            'name_en' => 'test',
            'name_nl' => 'test',
            'type' => 'text',
            'form' => Kitchen::class,
        ])->assertForbidden();
    }


	public function test_admin_can_delete_field() {
		$this->actingAs($this->admin)
			->delete(action('Admin\FieldController@destroy', $this->fields->first()))
			->assertSuccessful();
		$this->assertDatabaseMissing('fields', [
			'id' => $this->fields->first()->field
		]);
	}

    public function test_guest_cant_delete_field() {
        $this->delete(action('Admin\FieldController@destroy', $this->fields->first()))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_delete_field() {
        $this->actingAs($this->kitchen)->delete(action('Admin\FieldController@destroy', $this->fields->first()))->assertForbidden();
    }

	public function test_admin_can_edit_field() {
		$this->actingAs($this->admin)->patch(action('Admin\FieldController@edit', $this->fields->first()), [
			'name_en' => 'new name',
			'name_nl' => 'new name nl',
			'type' => 'text',
			'status' => 'required'
		])->assertSuccessful();
		$this->assertDatabaseHas('fields', [
			'name_en' => 'new name',
			'name_nl' => 'new name nl',
			'type' => 'text',
			'status' => 'required'
		]);
	}

    public function test_guest_cant_edit_field() {
        $this->patch(action('Admin\FieldController@edit', $this->fields->first()), [
            'name_en' => 'new name',
            'name_nl' => 'new name nl',
            'type' => 'text'
        ])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_edit_field() {
        $this->actingAs($this->kitchen)->patch(action('Admin\FieldController@edit', $this->fields->first()), [
            'name_en' => 'new name',
            'name_nl' => 'new name nl',
            'type' => 'text'
        ])->assertForbidden();
    }

    public function test_admin_can_order_list(){
	    $newOrder = $this->fields->sortByDesc('id')->pluck('id');
	    $this->actingAs($this->admin)->patch(action('Admin\FieldController@saveOrder', [
	        'order' => $newOrder->toArray()
        ]));
	    $ordered = Kitchen::fields()->pluck('id');
	    $this->assertEquals($newOrder,$ordered);
    }

    public function test_guest_cant_order_list() {
        $newOrder = $this->fields->sortByDesc('id')->pluck('id');
        $this->patch(action('Admin\FieldController@saveOrder', [
            'order' => $newOrder->toArray()
        ]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_order_list() {
        $newOrder = $this->fields->sortByDesc('id')->pluck('id');
        $this->actingAs($this->kitchen)->patch(action('Admin\FieldController@saveOrder', [
            'order' => $newOrder->toArray()
        ]))->assertForbidden();
    }
}
