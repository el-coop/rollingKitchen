<?php


namespace Tests\Feature\Kitchens;

use App\Models\Admin;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Pdf;
use App\Notifications\Admin\ApplicationResubmittedNotification;
use App\Notifications\Kitchen\ApplicationSubmittedNotification;
use ElCoop\Valuestore\Valuestore;
use Image;
use Illuminate\Http\UploadedFile;
use Storage;
use Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KitchenControllerTest extends TestCase {

    use RefreshDatabase;

    protected $settings;
    private $user;
    private $user1;

    public function setUp(): void {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('test.valuestore.json', '');
        $path = Storage::path('test.valuestore.json');
        $this->app->singleton('settings', function($app) use ($path) {
            return new Valuestore($path);
        });
        $settings = app('settings');
        $settings->put('general_registration_status', true);
        $settings->put('registration_year', 2018);
        $settings->put('application_text_en', 'test');
        $settings->put('application_success_text_en', 'test');

        $this->user = User::factory()->make();
        Kitchen::factory()->create()->user()->save($this->user);
        $this->user1 = User::factory()->make();
        Kitchen::factory()->create()->user()->save($this->user1);
        $this->settings = $this->app->settings;

    }

    public function test_guest_can_view_registration_form() {
        $this->get(action('Kitchen\KitchenController@create'))->assertSuccessful()
            ->assertViewIs('auth.register');
    }

    public function test_guest_cant_view_registration_form_when_registration_is_closed() {
        $settings = app('settings');
        $settings->put('general_registration_status', false);
        $this->get(action('Kitchen\KitchenController@create'))->assertRedirect(action('HomeController@show'));

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
        $photo = Photo::factory()->create([
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
        $photo = Photo::factory()->create([
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
        $photo = Photo::factory()->create([
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
            ->assertSee('id="reviewButton"', false);
    }

    public function test_kitchen_can_see_its_own_edit_page_with_unsubmitted_application() {
        $application = Application::factory()->make([
            'year' => intval($this->settings->get('registration_year')),
            'status' => 'new',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertViewIs('kitchen.edit')
            ->assertViewHas('kitchen', $this->user->user)
            ->assertViewHas('application', $application)
            ->assertSee("value: '{$application->length}'", false)
            ->assertSee('id="reviewButton"', false);
    }

    public function test_kitchen_can_see_its_own_edit_page_with_reopened_application() {
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'reopened',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertViewIs('kitchen.edit')
            ->assertViewHas('kitchen', $this->user->user)
            ->assertViewHas('application', $application)
            ->assertSee("value: '{$application->length}'", false)
            ->assertSee('id="reviewButton"', false);
    }

    public function test_kitchen_can_see_but_not_update_submitted_application() {
        $appliedText = $this->settings->get("application_success_text_{$this->user->language}");
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'pending',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertViewIs('kitchen.edit')
            ->assertViewHas('kitchen', $this->user->user)
            ->assertViewHas('application', $application)
            ->assertSee("value: '{$application->length}'", false)
            ->assertSee(str_replace(PHP_EOL, '<br>', $appliedText), false)
            ->assertDontSee('id="reviewButton"');
    }

    public function test_guest_cant_update_kitchen_data() {
        $this->patch(action('Kitchen\KitchenController@update', $this->user->user))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_other_kitchen_cant_update_kitchen_data() {
        $this->actingAs($this->user1)->patch(action('Kitchen\KitchenController@update', $this->user->user))->assertForbidden();
    }

    public function test_kitchen_can_update_kitchen_data_and_unsubmitted_application() {
        $services = Service::factory(3)->create();
        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'new',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
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
            'socket' => $socket->id,
            'length' => 1,
            'width' => 1,
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], 'nl')
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
        ]);
        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id,
        ]);
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'length' => 1,
            'width' => 1,
        ]);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect([
            'data' => 'test'
        ]), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertEquals(collect([
            'data' => 'test'
        ]), $updatedApplication->data);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect([
            'data' => 'test'
        ]), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertEquals(collect([
            'data' => 'test'
        ]), $updatedApplication->data);
        $this->assertDatabaseHas('application_service', [
            'application_id' => $application->id,
            'service_id' => $socket->id,
            'quantity' => 1
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

    public function test_kitchen_can_submit_unsubmitted_application() {
        $admin = User::factory()->make();
        Admin::factory()->create()->user()->save($admin);

        \Notification::fake();

        $services = Service::factory(3)->create();

        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();

        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'new',
        ]);
        $this->user->user->applications()->save($application);

        $application->products()->save(Product::factory()->make([
            'category' => 'menu'
        ]));

        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
            'kitchen' => [
                1 => 'test',
                2 => 'test',
                3 => 'test',
                4 => 'test',
                5 => 'test',
                7 => 'test',
            ],
            'application' => [
                8 => 2000,
                9 => 'these are like 10 chars'
            ],
            'services' => [
                $services->get(0)->id => 1,
                $services->get(1)->id => 0,
                $services->get(2)->id => 5
            ],
            'socket' => $socket->id,
            'length' => 1,
            'width' => 1,
            'review' => true
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], 'nl')
        ])->assertSessionHas('fireworks');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
        ]);
        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id,
        ]);
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'pending',
            'length' => 1,
            'width' => 1,
        ]);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect([1 => 'test',
            2 => 'test',
            3 => 'test',
            4 => 'test',
            5 => 'test',
            7 => 'test',]), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertEquals(collect([
            8 => 2000,
            9 => 'these are like 10 chars'
        ]), $updatedApplication->data);
        $this->assertDatabaseHas('application_service', [
            'application_id' => $application->id,
            'service_id' => $services->get(0)->id,
            'quantity' => 1
        ]);
        $this->assertDatabaseHas('application_service', [
            'application_id' => $application->id,
            'service_id' => $socket->id,
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

        Notification::assertSentTo([$application->kitchen->user], ApplicationSubmittedNotification::class);
        Notification::assertSentTo([$admin], \App\Notifications\Admin\ApplicationSubmittedNotification::class);
    }

    public function test_kitchen_must_accept_terms_to_submit_unsubmitted_application() {
        $admin = User::factory()->make();
        Admin::factory()->create()->user()->save($admin);
        Pdf::factory()->create([
            'name' => 'terms',
            "terms_and_conditions_{$this->user->language}" => true
        ]);

        Notification::fake();


        $services = Service::factory(3)->create();

        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();

        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'new',
        ]);
        $this->user->user->applications()->save($application);

        $application->products()->save(Product::factory()->make([
            'category' => 'menu'
        ]));

        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
            'kitchen' => [
                1 => 'test',
                2 => 'test',
                3 => 'test',
                4 => 'test',
                5 => 'test',
                7 => 'test',
            ],
            'application' => [
                8 => 2000,
                9 => 'these are like 10 chars'
            ],
            'services' => [
                $services->get(0)->id => 1,
                $services->get(1)->id => 0,
                $services->get(2)->id => 5
            ],
            'socket' => $socket->id,
            'length' => 1,
            'width' => 1,
            'review' => true
        ])->assertSessionHasErrors('terms');

        Notification::assertNotSentTo($application->kitchen->user, ApplicationSubmittedNotification::class);
    }

    public function test_kitchen_can_submit_reopened_application() {

        $admin = User::factory()->make();
        Admin::factory()->create()->user()->save($admin);

        Notification::fake();

        $services = Service::factory(3)->create();
        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'reopened',
        ]);

        $this->user->user->applications()->save($application);

        $application->products()->save(Product::factory()->make([
            'category' => 'menu'
        ]));

        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
            'kitchen' => [
                1 => 'test',
                2 => 'test',
                3 => 'test',
                4 => 'test',
                5 => 'test',
                7 => 'test',
            ],
            'application' => [
                8 => 2000,
                9 => 'these are like 10 chars'
            ],
            'services' => [
                $services->get(0)->id => 1,
                $services->get(1)->id => 0,
                $services->get(2)->id => 5
            ],
            'socket' => $socket->id,
            'length' => 1,
            'width' => 1,
            'review' => true
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], 'nl')
        ])->assertSessionHas('fireworks');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
        ]);
        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id,
        ]);
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'pending',
            'length' => 1,
            'width' => 1,
        ]);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect([1 => 'test',
            2 => 'test',
            3 => 'test',
            4 => 'test',
            5 => 'test',
            7 => 'test',]), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertEquals(collect([
            8 => 2000,
            9 => 'these are like 10 chars'
        ]), $updatedApplication->data);
        $this->assertDatabaseHas('application_service', [
            'application_id' => $application->id,
            'service_id' => $services->get(0)->id,
            'quantity' => 1
        ]);


        $this->assertDatabaseHas('application_service', [
            'application_id' => $application->id,
            'service_id' => $socket->id,
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

        Notification::assertSentTo([$admin], ApplicationResubmittedNotification::class);
    }


    public function test_kitchen_can_update_kitchen_data_but_not_submitted_application_data() {
        $services = Service::factory(3)->create();
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'pending',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
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
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], 'nl')
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
        ]);
        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id,
        ]);
        $this->assertDatabaseMissing('applications', [
            'id' => $application->id,
            'length' => 1,
            'width' => 1,
        ]);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect(['data' => 'test']), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertNotEquals(collect(['data' => 'test']), $updatedApplication->data);
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

    public function test_kitchen_can_update_kitchen_data_and_reopened_application_data() {
        $services = Service::factory(3)->create();
        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();
        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'reopened',
        ]);
        $this->user->user->applications()->save($application);
        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
            'kitchen' => [
                'data' => 'test'
            ],
            'application' => [
                'data' => 'test',
                8 => 2000
            ],
            'services' => [
                $services->get(0)->id => 1,
                $services->get(1)->id => 0,
                $services->get(2)->id => 5
            ],
            'socket' => 0,
            'length' => 1,
            'width' => 1,
        ])->assertRedirect()->assertSessionHas('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], 'nl')
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'test',
            'email' => 'test@best.rest',
        ]);
        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'length' => 1,
            'width' => 1,
        ]);
        $kitchen = Kitchen::find($this->user->user->id);
        $this->assertEquals(collect(['data' => 'test']), $kitchen->data);
        $updatedApplication = Application::find($application->id);
        $this->assertEquals(collect(['data' => 'test', 8 => 2000]), $updatedApplication->data);

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
        $this->assertDatabaseMissing('application_service', [
            'application_id' => $application->id,
            'service_id' => 0,
            'quantity' => 1
        ]);
    }

    public function test_kitchen_uploaded_photo_gets_processed_by_width() {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('photo.png', 1000, 500);
        $this->actingAs($this->user)->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
            'photo' => $file
        ]);
        $photo = $this->user->user->photos->first();
        $path = $photo->file;
        $processedImage = Image::make(Storage::disk('local')->get('public/photos/' . $path));
        $this->assertEquals(800, $processedImage->width());
        $this->assertEquals(400, $processedImage->height());
        $this->assertEquals('image/jpeg', $processedImage->mime());
    }

    public function test_kitchen_uploaded_photo_gets_processed_by_height() {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('photo.png', 500, 1000);
        $this->actingAs($this->user)->post(action('Kitchen\KitchenController@storePhoto', $this->user->user), [
            'photo' => $file
        ]);
        $photo = $this->user->user->photos->first();
        $path = $photo->file;
        $processedImage = Image::make(Storage::disk('local')->get('public/photos/' . $path));
        $this->assertEquals(250, $processedImage->width());
        $this->assertEquals(500, $processedImage->height());
        $this->assertEquals('image/jpeg', $processedImage->mime());
    }

    public function test_kitchen_can_see_applications_if_exists() {
        $pastApplication = Application::factory()->make(['year' => '2012']);
        $this->user->user->applications()->save($pastApplication);
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSee(__('kitchen/kitchen.pastApplications'))
            ->assertSee($pastApplication->year);
    }

    public function test_past_applications_tab_doesnt_exists_if_there_are_no_past_applications() {
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertDontSee(__('kitchen/kitchen.pastApplications'));
    }

    public function test_kitchen_can_delete_self_and_all_applications() {
        $pastApplication = Application::factory()->make(['year' => '2012']);
        $this->user->user->applications()->save($pastApplication);
        $this->actingAs($this->user)->delete(action('Kitchen\KitchenController@destroy', $this->user->user))
            ->assertRedirect(action('HomeController@show'));

        $this->assertDatabaseMissing('users', [
            'user_type' => Kitchen::class,
            'user_id' => $this->user->user->id
        ]);

        $this->assertDatabaseMissing('applications', [
            'kitchen_id' => $this->user->user->id
        ]);

        $this->assertDatabaseMissing('kitchens', [
            'id' => $this->user->user->id
        ]);
    }

    public function test_guest_cant_delete_kitchen_and_application() {
        $this->delete(action('Kitchen\KitchenController@destroy', $this->user->user))
            ->assertRedirect(action('Auth\LoginController@showLoginForm'));

        $this->assertDatabaseHas('users', [
            'user_type' => Kitchen::class,
            'user_id' => $this->user->user->id
        ]);

        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id
        ]);
    }

    public function test_other_kitchen_cant_delete_kitchen_and_application() {
        $this->actingAs($this->user1)->delete(action('Kitchen\KitchenController@destroy', $this->user->user))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'user_type' => Kitchen::class,
            'user_id' => $this->user->user->id
        ]);

        $this->assertDatabaseHas('kitchens', [
            'id' => $this->user->user->id
        ]);
    }

    public function test_kitchen_cant_submit_application_without_mandatory_service() {
        $admin = User::factory()->make();
        Admin::factory()->create()->user()->save($admin);
        $services = Service::factory(3)->create();
        $mandatoryService = Service::factory()->create([
           'mandatory' => 1
        ]);
        $socket = Service::factory(3)->create([
            'category' => 'socket'
        ])->random();

        $application = Application::factory()->make([
            'year' => $this->settings->get('registration_year'),
            'status' => 'new',
        ]);
        $this->user->user->applications()->save($application);

        $application->products()->save(Product::factory()->make([
            'category' => 'menu'
        ]));

        $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), [
            'name' => 'test',
            'email' => 'test@best.rest',
            'language' => 'nl',
            'kitchen' => [
                1 => 'test',
                2 => 'test',
                3 => 'test',
                4 => 'test',
                5 => 'test',
                7 => 'test',
            ],
            'application' => [
                8 => 2000,
                9 => 'these are like 10 chars'
            ],
            'services' => [
                $services->get(0)->id => 1,
                $services->get(1)->id => 0,
                $services->get(2)->id => 5
            ],
            'socket' => $socket->id,
            'length' => 1,
            'width' => 1,
            'review' => true
        ])->assertSessionHasErrors(['services' =>'The services field must contain entries for: ' . "$mandatoryService->id" . '.']);
    }
}

