<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FieldTest extends TestCase {
	use RefreshDatabase;
	protected $admin;
	
	protected function setUp() {
		parent::setUp();
		$this->admin = factory(User::class)->make();
		factory(Admin::class)->create()->user()->save($this->admin);
	}
	
	public function test_can_see_Kitchen_field_list() {
		$this->actingAs($this->admin)->get(action('Admin\FieldController@index', 'Kitchen'))->assertSuccessful()->assertViewIs('admin.fields');
	}
	
	public function test_can_see_application_field_list() {
		$this->actingAs($this->admin)->get(action('Admin\FieldController@index', 'Application'))->assertSuccessful()->assertViewIs('admin.fields');
	}
	
	public function test_create_field() {
		$this->actingAs($this->admin)->post(action('Admin\FieldController@create'), [
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
		])->assertSuccessful()->assertJson([
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
		]);
		$this->assertDatabaseHas('fields', [
			'name_en' => 'test',
			'name_nl' => 'test',
			'type' => 'text',
			'form' => Kitchen::class,
		]);
	}
	
	public function test_delete_field() {
		$field = factory(Field::class)->create();
		$this->actingAs($this->admin)
			->delete(action('Admin\FieldController@destroy', $field))
			->assertSuccessful();
		$this->assertDatabaseMissing('fields', [
			'id' => $field->id
		]);
	}
	
	public function test_edit_field() {
		$field = factory(Field::class)->create();
		
		$this->actingAs($this->admin)->patch(action('Admin\FieldController@edit', $field), [
			'name_en' => 'new name',
			'name_nl' => 'new name nl',
			'type' => 'text'
		])->assertSuccessful();
		$this->assertDatabaseHas('fields', [
			'name_en' => 'new name',
			'name_nl' => 'new name nl',
			'type' => 'text'
		]);
	}
}