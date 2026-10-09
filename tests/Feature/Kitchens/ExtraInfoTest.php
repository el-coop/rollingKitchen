<?php

namespace Tests\Feature\Kitchens;

use App\Models\Admin;
use App\Models\Application;
use App\Models\ElectricDevice;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\User;
use App\Notifications\Admin\ExtraInfoSubmittedNotification;
use ElCoop\Valuestore\Valuestore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Storage;
use Tests\TestCase;

class ExtraInfoTest extends TestCase {

    use RefreshDatabase;

    protected $settings;
    protected $admin;
    protected $user;
    protected $user1;
    protected $application;
    protected $dish;
    protected $secondDish;

    public function setUp(): void {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('test.valuestore.json', '');
        $path = Storage::path('test.valuestore.json');
        $this->app->singleton('settings', function ($app) use ($path) {
            return new Valuestore($path);
        });
        $this->settings = app('settings');
        $this->settings->put('general_registration_status', false);
        $this->settings->put('registration_year', 2018);
        $this->settings->put('application_text_en', 'test');
        $this->settings->put('application_text_nl', 'test');
        $this->settings->put('application_success_text_en', 'submitted text');
        $this->settings->put('application_success_text_nl', 'submitted text');
        $this->settings->put('application_extra_info_text_en', 'extra info instructions');
        $this->settings->put('application_extra_info_text_nl', 'extra info instructions');
        $this->settings->put('application_extra_info_menu_text_en', 'menu intro');
        $this->settings->put('application_extra_info_menu_text_nl', 'menu intro');

        $this->admin = User::factory()->make();
        Admin::factory()->create()->user()->save($this->admin);
        $this->user = User::factory()->make();
        Kitchen::factory()->create()->user()->save($this->user);
        $this->user1 = User::factory()->make();
        Kitchen::factory()->create()->user()->save($this->user1);

        $this->application = Application::factory()->make([
            'year' => 2018,
            'status' => 'selected',
        ]);
        $this->user->user->applications()->save($this->application);
        $this->dish = Product::factory()->make(['category' => 'menu']);
        $this->secondDish = Product::factory()->make(['category' => 'menu']);
        $this->application->products()->saveMany([$this->dish, $this->secondDish]);
    }

    public function test_guest_cant_update_extra_info() {
        $this->patch(action('Kitchen\KitchenController@update', $this->user->user), $this->extraInfo())
            ->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_other_kitchen_cant_update_extra_info() {
        $this->actingAs($this->user1)->patch(action('Kitchen\KitchenController@update', $this->user->user), $this->extraInfo())
            ->assertForbidden();
    }

    public function test_kitchen_sees_extra_info_tabs_on_selected_application() {
        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertSee('extra info instructions')
            ->assertSee('menu intro')
            ->assertSee('<toggle-chooser>', false)
            ->assertSee('&quot;name&quot;:&quot;outside_serving&quot;', false)
            ->assertSee("religious_notes[{$this->dish->id}]", false)
            ->assertSee("/kitchen/applications/{$this->application->id}/devices", false)
            ->assertSee('id="reviewExtraButton"', false)
            ->assertDontSee('id="reviewButton"', false);
    }

    public function test_kitchen_doesnt_see_extra_info_tabs_on_pending_application() {
        $this->application->status = 'pending';
        $this->application->save();

        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertSee('submitted text')
            ->assertDontSee('<toggle-chooser>', false)
            ->assertDontSee('&quot;name&quot;:&quot;outside_serving&quot;', false)
            ->assertDontSee('id="reviewExtraButton"', false);
    }

    public function test_kitchen_sees_submitted_extra_info_read_only_on_accepted_application() {
        $this->application->status = 'accepted';
        $this->application->extra_info_submitted_at = now();
        $this->application->save();

        $this->actingAs($this->user)->get(action('Kitchen\KitchenController@edit', $this->user->user))
            ->assertSuccessful()
            ->assertSee('&quot;name&quot;:&quot;outside_serving&quot;', false)
            ->assertDontSee("/kitchen/applications/{$this->application->id}/devices", false)
            ->assertDontSee('id="reviewExtraButton"', false);
    }

    public function test_kitchen_can_save_extra_info_without_submitting() {
        Notification::fake();

        $this->saveExtraInfo([
            'outside_serving' => 1,
            'other_details' => 'we need shade',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'status' => 'selected',
            'outside_serving' => true,
            'outside_serving_depth' => null,
            'open_flame' => null,
            'other_details' => 'we need shade',
            'extra_info_submitted_at' => null,
        ]);
        Notification::assertNothingSent();
    }

    public function test_kitchen_can_submit_extra_info() {
        Notification::fake();
        $this->application->electricDevices()->save(ElectricDevice::factory()->make());

        $this->saveExtraInfo($this->extraInfo())->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'status' => 'selected',
            'outside_serving' => true,
            'outside_serving_depth' => 2.5,
            'path_distance' => 4,
            'own_terrace_furniture' => false,
            'needs_backstage' => true,
            'cooks_in_backstage' => false,
            'open_flame' => false,
            'uses_gas' => true,
            'uses_electric_devices' => true,
            'deep_fryer' => false,
            'religious_dishes' => true,
            'vegan_dishes' => true,
            'other_details' => 'nothing else',
        ]);
        $this->assertNotNull($this->application->fresh()->extra_info_submitted_at);
        $this->assertDatabaseHas('products', [
            'id' => $this->dish->id,
            'religious_note' => 'halal',
            'vegan' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $this->secondDish->id,
            'religious_note' => null,
            'vegan' => true,
        ]);
        Notification::assertSentTo($this->admin, ExtraInfoSubmittedNotification::class);
    }

    public function test_kitchen_can_edit_extra_info_after_submitting() {
        Notification::fake();
        $this->application->extra_info_submitted_at = now();
        $this->application->save();

        $this->saveExtraInfo([
            'deep_fryer' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'deep_fryer' => true,
        ]);
    }

    public function test_kitchen_cant_submit_extra_info_without_answers() {
        $this->saveExtraInfo([
            'review_extra' => 1,
        ])->assertSessionHasErrors([
            'outside_serving',
            'path_distance',
            'own_terrace_furniture',
            'needs_backstage',
            'open_flame',
            'uses_gas',
            'uses_electric_devices',
            'deep_fryer',
            'religious_dishes',
            'vegan_dishes',
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'extra_info_submitted_at' => null,
        ]);
    }

    public function test_kitchen_cant_submit_extra_info_without_follow_up_answers() {
        $this->saveExtraInfo(array_merge($this->extraInfo(), [
            'outside_serving_depth' => '',
            'cooks_in_backstage' => null,
            'religious_notes' => [$this->dish->id => ''],
            'vegan_products' => [],
        ]))->assertSessionHasErrors([
            'outside_serving_depth',
            'cooks_in_backstage',
            'uses_electric_devices',
            'religious_dishes',
            'vegan_dishes',
        ]);
    }

    public function test_kitchen_answering_no_clears_follow_up_answers() {
        $this->application->outside_serving = true;
        $this->application->outside_serving_depth = 3;
        $this->application->needs_backstage = true;
        $this->application->cooks_in_backstage = true;
        $this->application->save();
        $device = ElectricDevice::factory()->make();
        $this->application->electricDevices()->save($device);
        $this->dish->vegan = true;
        $this->dish->religious_note = 'kosher';
        $this->dish->save();

        $this->saveExtraInfo([
            'outside_serving' => 0,
            'outside_serving_depth' => 3,
            'needs_backstage' => 0,
            'cooks_in_backstage' => 1,
            'uses_electric_devices' => 0,
            'religious_dishes' => 0,
            'religious_notes' => [$this->dish->id => 'kosher'],
            'vegan_dishes' => 0,
            'vegan_products' => [$this->dish->id],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'outside_serving' => false,
            'outside_serving_depth' => null,
            'needs_backstage' => false,
            'cooks_in_backstage' => null,
        ]);
        $this->assertDatabaseMissing('electric_devices', [
            'id' => $device->id,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $this->dish->id,
            'religious_note' => null,
            'vegan' => false,
        ]);
    }

    public function test_kitchen_cant_update_extra_info_on_pending_application() {
        $this->application->status = 'pending';
        $this->application->save();

        $this->saveExtraInfo($this->extraInfo())->assertSessionHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'outside_serving' => null,
            'extra_info_submitted_at' => null,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $this->secondDish->id,
            'vegan' => false,
        ]);
    }

    public function test_kitchen_cant_mark_dishes_of_other_kitchen() {
        $otherApplication = Application::factory()->make(['year' => 2018, 'status' => 'selected']);
        $this->user1->user->applications()->save($otherApplication);
        $otherDish = Product::factory()->make(['category' => 'menu']);
        $otherApplication->products()->save($otherDish);

        $this->saveExtraInfo([
            'religious_dishes' => 1,
            'religious_notes' => [$otherDish->id => 'halal'],
            'vegan_dishes' => 1,
            'vegan_products' => [$otherDish->id],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $otherDish->id,
            'religious_note' => null,
            'vegan' => false,
        ]);
    }

    public function test_extra_info_validation() {
        $this->saveExtraInfo([
            'outside_serving' => 'maybe',
            'outside_serving_depth' => 'deep',
            'path_distance' => -1,
        ])->assertSessionHasErrors(['outside_serving', 'outside_serving_depth', 'path_distance']);
    }

    private function saveExtraInfo($data) {
        return $this->actingAs($this->user)->patch(action('Kitchen\KitchenController@update', $this->user->user), array_merge([
            'name' => 'test kitchen',
            'email' => 'test@best.com',
            'language' => 'en',
            'kitchen' => [1 => 'test'],
        ], $data));
    }

    private function extraInfo() {
        return [
            'name' => 'test kitchen',
            'email' => 'test@best.com',
            'language' => 'en',
            'kitchen' => [1 => 'test'],
            'outside_serving' => 1,
            'outside_serving_depth' => 2.5,
            'path_distance' => 4,
            'own_terrace_furniture' => 0,
            'needs_backstage' => 1,
            'cooks_in_backstage' => 0,
            'open_flame' => 0,
            'uses_gas' => 1,
            'uses_electric_devices' => 1,
            'deep_fryer' => 0,
            'religious_dishes' => 1,
            'religious_notes' => [$this->dish->id => 'halal', $this->secondDish->id => ''],
            'vegan_dishes' => 1,
            'vegan_products' => [$this->secondDish->id],
            'other_details' => 'nothing else',
            'review_extra' => 1,
        ];
    }
}
