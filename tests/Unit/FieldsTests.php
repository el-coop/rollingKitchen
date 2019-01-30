<?php

namespace Tests\Unit;

use App\Models\Field;
use App\Models\Kitchen;
use App\Models\Worker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FieldsTests extends TestCase {
	
	use RefreshDatabase;
	
	private $encryptedField;
	private $requiredField;
	private $protectedField;
	private $noneField;
	private $worker;
	
	protected function setUp() {
		parent::setUp();
		
		foreach (['encrypted', 'required', 'protected', 'none'] as $order => $status) {
			$this->{"{$status}Field"} = factory(Field::class)->create([
				'form' => Worker::class,
				'order' => $order + 1,
				'status' => $status
			]);
		}
		
		$this->worker = factory(Worker::class)->create();
	}
	
	public function test_encrypts_encrypted_fields() {
		Crypt::shouldReceive('encrypt')->once()->with('encValue', true)->andReturn('encrypted');
		
		$this->worker->data = [
			$this->encryptedField->id => 'encValue',
			$this->requiredField->id => 'required',
			$this->protectedField->id => 'protected',
			$this->noneField->id => 'none',
		];
		$this->worker->save();
		
		$this->assertDatabaseHas('workers', [
			'id' => $this->worker->id,
			'data' => json_encode([
				$this->encryptedField->id => 'encrypted',
				$this->requiredField->id => 'required',
				$this->protectedField->id => 'protected',
				$this->noneField->id => 'none',
			])
		]);
	}
	
	public function test_decrypts_encrypted_fields() {
		Crypt::shouldReceive('encrypt')->once()->with('encValue', true)->andReturn('encrypted');
		Crypt::shouldReceive('decrypt')->once()->with('encrypted', true)->andReturn('decrypted');
		
		$this->worker->data = [
			$this->encryptedField->id => 'encValue',
			$this->requiredField->id => 'required',
			$this->protectedField->id => 'protected',
			$this->noneField->id => 'none',
		];
		$this->worker->save();
		
		$this->assertArraySubset([
			$this->encryptedField->id => 'decrypted',
			$this->requiredField->id => 'required',
			$this->protectedField->id => 'protected',
			$this->noneField->id => 'none',
		], $this->worker->data);
	}
	
	public function test_field_getRequiredFields_returns_relevant_required_and_encrypted_fields() {
		$kitchenRequiredField = factory(Field::class)->create([
			'status' => 'required',
			'form' => Kitchen::class
		]);
		$kitchenEncryptedField = factory(Field::class)->create([
			'status' => 'encrypted',
			'form' => Kitchen::class
		]);
		
		$requiredFields = Field::getRequiredFields(Worker::class);
		
		$this->assertTrue($requiredFields->has("worker.{$this->requiredField->id}"));
		$this->assertTrue($requiredFields->has("worker.{$this->encryptedField->id}"));
		$this->assertFalse($requiredFields->has("worker.{$this->noneField->id}"));
		$this->assertFalse($requiredFields->has("worker.{$this->protectedField->id}"));
		$this->assertFalse($requiredFields->has("worker.{$this->protectedField->id}"));
		$this->assertFalse($requiredFields->has("kitchen.{$kitchenEncryptedField->id}"));
		$this->assertFalse($requiredFields->has("kitchen.{$kitchenRequiredField->id}"));
	}
}
