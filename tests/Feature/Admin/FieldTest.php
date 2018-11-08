<?php

namespace Tests\Feature\Admin;

use App\Models\Field;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FieldTest extends TestCase {
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_create_field() {
        $response = $this->post(action('Admin\FieldController@create'), ['name' => 'test', 'type' => 'text']);
        $response->assertStatus(201);
        $this->assertDatabaseHas('fields', ['name' => 'test', 'type' => 'text']);
    }

    public function test_delete_field() {
        $field = factory(Field::class)->create();
        $this->assertDatabaseHas('fields', ['name' => $field->name, 'type' => $field->type]);
        $response = $this->delete(action('Admin\FieldController@delete', $field));
        $response->assertStatus(200 );
        $this->assertDatabaseMissing('fields', ['name' => $field->name, 'type' => $field->type]);
    }

    public function test_edit_field(){
        $field = factory(Field::class)->create();
        $this->assertDatabaseHas('fields', ['name' => $field->name, 'type' => $field->type]);
        $response = $this->patch(action('Admin\FieldController@edit', $field), ['name' => 'new name', 'type' => 'text']);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('fields', ['name' => $field->name, 'type' => $field->type]);
        $this->assertDatabaseHas('fields', ['name' => 'new name', 'type' => 'text']);
    }
}
