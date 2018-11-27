<?php

namespace Tests\Feature\Kitchens;

use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KitchenControllerTest extends TestCase {
	
	use RefreshDatabase;
	
	public function setUp() {
		parent::setUp();
		
		$this->user = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->user);
		
		
		$this->user1 = factory(User::class)->make();
		factory(Kitchen::class)->create()->user()->save($this->user1);
	}
	
	public function test_guest_can_view_registration_form() {
		$this->get(action('Kitchen\KitchenController@create'))->assertSuccessful()
			->assertViewIs('auth.register');
	}
	
	
	public function test_kitchen_viewing_registration_gets_redirected_to_edit() {
		$this->actingAs($this->user)->get(action('Kitchen\KitchenController@create'))->assertRedirect(action('Kitchen\KitchenController@edit', $this->user->user));
	}
	
	public function test_kitchen_viewing_login_gets_redirected_to_edit() {
		$this->actingAs($this->user)->get(action('Auth\LoginController@login'))->assertRedirect(action('Kitchen\KitchenController@edit', $this->user->user));
	}
	
	public function test_registers_new_kitchen() {
		$kitchenCount = Kitchen::count();
		$this->post(action('Kitchen\KitchenController@store'), [
			'name' => 'test kitchen',
			'email' => 'test@best.rest',
			'language' => 'nl',
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertRedirect();
		
		$this->assertAuthenticated();
		
		$this->assertDatabaseHas('users', [
			'name' => 'test kitchen',
			'email' => 'test@best.rest',
			'language' => 'nl',
		]);
		
		
		$this->assertCount($kitchenCount + 1, Kitchen::All());
	}
	
	public function test_validates_kitchen_registartion() {
		$this->post(action('Kitchen\KitchenController@store'), [
			'name' => '1',
			'email' => 'rest',
			'language' => 'de',
			'password' => '6',
			'password_confirmation' => 'as',
		])->assertSessionHasErrors(['name', 'email', 'language', 'password']);
	}
	
	
	public function test_kitchen_cant_reregister() {
		$kitchenCount = Kitchen::count();
		
		$this->actingAs($this->user)->post(action('Kitchen\KitchenController@store'), [
			'name' => 'test kitchen',
			'email' => 'test@best.rest',
			'language' => 'nl',
			'password' => '123456',
			'password_confirmation' => '123456',
		])->assertRedirect(action('Kitchen\KitchenController@edit', $this->user->user));
		
		$this->assertCount($kitchenCount, Kitchen::All());
	}
	
	public function test_kitchen_can_upload_photo() {
		Storage::fake('local');
		
		$file = UploadedFile::fake()->image('photo.jpg');
		$this->actingAs($this->user)->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
			'photo' => $file
		])->assertJson([
			'kitchen_id' => $this->user->user->id,
			'file' => $file->hashName()
		]);
		
		Storage::disk('local')->assertExists("public/photos/{$file->hashName()}");
	}
	
	public function test_kitchen_can_delete_photo() {
		Storage::fake('local');
		$file = UploadedFile::fake()->image('photo.jpg');
		$file->store('public/photos');
		$photo = factory(Photo::class)->create([
			'kitchen_id' => $this->user->user->id,
			'file' => $file->hashName(),
		]);
		
		$this->actingAs($this->user)->delete(action('Kitchen\KitchenController@destroyPhoto', [
			'kitchen' => $this->user->user,
			'photo' => $photo
		]))->assertSuccessful()->assertJson([
			'success' => true
		]);
		
		Storage::disk('local')->assertMissing("public/photos/{$file->hashName()}");
	}
	
	public function test_guest_cant_upload_file() {
		Storage::fake('local');
		
		$file = UploadedFile::fake()->image('photo.jpg');
		$this->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
			'photo' => $file
		])->assertRedirect(action('Auth\LoginController@login'));
		
		Storage::disk('local')->assertMissing("public/photos/{$file->hashName()}");
	}
	
	public function test_guest_cant_delete_photo() {
		$file = UploadedFile::fake()->image('photo.jpg');
		$file->store('public/photos');
		$photo = factory(Photo::class)->create([
			'kitchen_id' => $this->user->user->id,
			'file' => $file->hashName(),
		]);
		
		$this->delete(action('Kitchen\KitchenController@destroyPhoto', [
			'kitchen' => $this->user->user,
			'photo' => $photo
		]))->assertRedirect(action('Auth\LoginController@login'));
		
		
		Storage::disk('local')->assertExists("public/photos/{$file->hashName()}");
	}
	
	public function test_other_kitchen_cant_upload_file() {
		Storage::fake('local');
		
		$file = UploadedFile::fake()->image('photo.jpg');
		$this->actingAs($this->user1)->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
			'photo' => $file
		])->assertForbidden();
		
		Storage::disk('local')->assertMissing("public/photos/{$file->hashName()}");
	}
	
	public function test_other_kitchen_cant_delete_photo() {
		$file = UploadedFile::fake()->image('photo.jpg');
		$file->store('public/photos');
		$photo = factory(Photo::class)->create([
			'kitchen_id' => $this->user->user->id,
			'file' => $file->hashName(),
		]);
		
		$this->actingAs($this->user1)->delete(action('Kitchen\KitchenController@destroyPhoto', [
			'kitchen' => $this->user->user,
			'photo' => $photo
		]))->assertForbidden();
		
		
		Storage::disk('local')->assertExists("public/photos/{$file->hashName()}");
	}
	
	
	public function test_cant_upload_non_image_file() {
		Storage::fake('local');
		
		$file = UploadedFile::fake()->image('photo.pdf');
		$this->actingAs($this->user)->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
			'photo' => $file
		])->assertSessionHasErrors('photo');
		
		Storage::disk('local')->assertMissing("public/photos/{$file->hashName()}");
	}
	
	
	public function test_guest_cant_see_kitchen_edit_view() {
		$this->get(action('Kitchen\KitchenController@edit', $this->user->user))->assertRedirect(action('Auth\LoginController@login'));
		
	}
	
	public function test_other_kitchen_cant_see_kitchen_edit_view() {
		$this->actingAs($this->user1)->get(action('Kitchen\KitchenController@edit', $this->user->user))->assertForbidden();
	}
	
	public function test_kitchen_can_see_its_own_edit_page_with_fresh_application() {
		$this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
			->assertSuccessful()
			->assertViewIs('kitchen.edit')
			->assertViewHas('kitchen', $this->user->user)
			->assertSee('id="reviewButton"');
	}
	
	public function test_kitchen_can_see_its_own_edit_page_with_unsubmitted_application() {
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'new',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
			->assertSuccessful()
			->assertViewIs('kitchen.edit')
			->assertViewHas('kitchen', $this->user->user)
			->assertViewHas('application', $application)
			->assertSee("value: '{$application->length}'")
			->assertSee('id="reviewButton"');
	}
	
	public function test_kitchen_can_see_its_own_edit_page_with_reopened_application() {
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'reopened',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
			->assertSuccessful()
			->assertViewIs('kitchen.edit')
			->assertViewHas('kitchen', $this->user->user)
			->assertViewHas('application', $application)
			->assertSee("value: '{$application->length}'")
			->assertSee('id="reviewButton"');
	}
	
	public function test_kitchen_can_see_but_not_update_submitted_application() {
		$appliedText = factory(Setting::class)->create([
			'name' => "application_text_{$this->user->language}"
		]);
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'pending',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
			->assertSuccessful()
			->assertViewIs('kitchen.edit')
			->assertViewHas('kitchen', $this->user->user)
			->assertViewHas('application', $application)
			->assertSee("value: '{$application->length}'")
			->assertSee($appliedText->value)
			->assertDontSee('id="reviewButton"');
	}
	
	public function test_guest_cant_update_kitchen_data() {
		$this->patch(action('Kitchen\KitchenController@update', $this->user->user))->assertRedirect(action('Auth\LoginController@login'));
	}
	
	public function test_other_kitchen_cant_update_kitchen_data() {
		$this->actingAs($this->user1)->patch(action('Kitchen\KitchenController@update', $this->user->user))->assertForbidden();
	}
	
	public function test_kitchen_can_update_kitchen_data_and_unsubmitted_application() {
		$services = factory(Service::class, 3)->create();
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'new',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
			'name' => 'test',
			'email' => 'test@best.rest',
			'kitchen' => [
				'data' => 'test'
			],
			'application' => [
				'data' => 'test'
			],
			'services' => [
				$services->get(0)->id => 1,
				$services->get(1)->id => 0,
				$services->get(2)->id => 5
			],
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		])->assertRedirect()->assertSessionHas('success', true);
		
		$this->assertDatabaseHas('users', [
			'id' => $this->user->id,
			'name' => 'test',
			'email' => 'test@best.rest',
		]);
		$this->assertDatabaseHas('kitchens', [
			'id' => $this->user->user->id,
			'data' => json_encode([
				'data' => 'test'
			])
		]);
		$this->assertDatabaseHas('applications', [
			'id' => $application->id,
			'data' => json_encode([
				'data' => 'test'
			]),
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		]);
		
		$this->assertDatabaseHas('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(0)->id,
			'quantity' => 1
		]);
		$this->assertDatabaseHas('application_service', [
				'application_id' => $application->id,
				'service_id' => $services->get(2)->id,
				'quantity' => 5
			]
		);
		
		$this->assertDatabaseMissing('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(1)->id,
		]);
	}
	
	
	public function test_kitchen_can_update_kitchen_data_but_not_submitted_application() {
		$services = factory(Service::class, 3)->create();
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'pending',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
			'name' => 'test',
			'email' => 'test@best.rest',
			'kitchen' => [
				'data' => 'test'
			],
			'application' => [
				'data' => 'test'
			],
			'services' => [
				$services->get(0)->id => 1,
				$services->get(1)->id => 0,
				$services->get(2)->id => 5
			],
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		])->assertRedirect()->assertSessionHas('success', true);
		
		$this->assertDatabaseHas('users', [
			'id' => $this->user->id,
			'name' => 'test',
			'email' => 'test@best.rest',
		]);
		$this->assertDatabaseHas('kitchens', [
			'id' => $this->user->user->id,
			'data' => json_encode([
				'data' => 'test'
			])
		]);
		$this->assertDatabaseMissing('applications', [
			'id' => $application->id,
			'data' => json_encode([
				'data' => 'test'
			]),
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		]);
		
		$this->assertDatabaseMissing('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(0)->id,
			'quantity' => 1
		]);
		$this->assertDatabaseMissing('application_service', [
				'application_id' => $application->id,
				'service_id' => $services->get(2)->id,
				'quantity' => 5
			]
		);
	}
	
	public function test_kitchen_can_update_kitchen_data_and_reopened_application() {
		$services = factory(Service::class, 3)->create();
		$application = factory(Application::class)->make([
			'year' => Setting::registrationYear()->value,
			'status' => 'reopened',
		]);
		$this->user->user->applications()->save($application);
		$this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
			'name' => 'test',
			'email' => 'test@best.rest',
			'kitchen' => [
				'data' => 'test'
			],
			'application' => [
				'data' => 'test'
			],
			'services' => [
				$services->get(0)->id => 1,
				$services->get(1)->id => 0,
				$services->get(2)->id => 5
			],
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		])->assertRedirect()->assertSessionHas('success', true);
		
		$this->assertDatabaseHas('users', [
			'id' => $this->user->id,
			'name' => 'test',
			'email' => 'test@best.rest',
		]);
		$this->assertDatabaseHas('kitchens', [
			'id' => $this->user->user->id,
			'data' => json_encode([
				'data' => 'test'
			])
		]);
		$this->assertDatabaseHas('applications', [
			'id' => $application->id,
			'data' => json_encode([
				'data' => 'test'
			]),
			'socket' => 5,
			'length' => 1,
			'width' => 1,
		]);
		
		$this->assertDatabaseHas('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(0)->id,
			'quantity' => 1
		]);
		$this->assertDatabaseHas('application_service', [
				'application_id' => $application->id,
				'service_id' => $services->get(2)->id,
				'quantity' => 5
			]
		);
		
		$this->assertDatabaseMissing('application_service', [
			'application_id' => $application->id,
			'service_id' => $services->get(1)->id,
		]);
	}
}