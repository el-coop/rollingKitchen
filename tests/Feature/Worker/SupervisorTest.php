<?php

namespace Tests\Feature\Worker;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Kitchen;
use App\Models\Shift;
use App\Models\ShiftWorker;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupervisorTest extends TestCase {
    use RefreshDatabase;
    protected $admin;
    protected $kitchen;
    protected $worker;
    protected $accountant;
    protected $workplace;
    protected $supervisor;
    protected $shift;
    protected $shiftWorker;

    protected function setUp(): void {
        parent::setUp();
        $this->admin = User::factory()->make();
        Admin::factory()->create()->user()->save($this->admin);
        $this->kitchen = User::factory()->make();
        Kitchen::factory()->create()->user()->save($this->kitchen);
        $this->accountant = User::factory()->make();
        Accountant::factory()->create()->user()->save($this->accountant);
        $this->worker = User::factory()->make();
        Worker::factory()->create()->user()->save($this->worker);
        $this->workplace = Workplace::factory()->create();
        WorkFunction::factory(3)->make()->each(function ($workFunction) {
            $this->workplace->workFunctions()->save($workFunction);
        });
        $this->supervisor = User::factory()->make();
        Worker::factory()->create(['supervisor' => true])->user()->save($this->supervisor);
        $this->supervisor->user->workplaces()->attach($this->workplace);
        $this->worker->user->workplaces()->attach($this->workplace);
        $this->shift = Shift::factory()->make([
            'hours' => 5,
            'date' =>  \Carbon\Carbon::create(app('settings')->get('registration_year'), 1,1)->format('Y-m-d')
        ]);
        $this->workplace->shifts()->save($this->shift);
        $this->shiftWorker = User::factory()->make();
        Worker::factory()->create()->user()->save($this->shiftWorker);
        $this->shiftWorker->user->workplaces()->attach($this->workplace);
        $this->shift->workers()->attach($this->shiftWorker->user, [
            'start_time' => '20:00',
            'end_time' => '22:00',
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);

    }

    public function test_worker_cant_see_manage_workplace_tab() {
        $this->actingAs($this->worker)->get(action('Worker\WorkerController@index', $this->worker->user))
            ->assertSuccessful()
            ->assertDontSee(__('worker/supervisor.manageWorkers'));
    }

    public function test_supervisor_can_see_manage_workplace_tab() {
        $this->actingAs($this->supervisor)->get(action('Worker\WorkerController@index', $this->supervisor->user))
            ->assertSuccessful()
            ->assertSee(__('worker/supervisor.manageWorkers'));
    }

    public function test_guest_cant_create_worker() {
        $this->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_create_worker() {
        $this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
    }

    public function test_admin_cant_create_worker() {
        $this->actingAs($this->admin)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
    }

    public function test_accountant_cant_create_worker() {
        $this->actingAs($this->accountant)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
    }

    public function test_worker_cant_create_worker() {
        $this->actingAs($this->worker)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertForbidden();
    }

    public function test_supervisor_can_create_worker() {
        $this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@createWorker', $this->workplace))->assertSuccessful()
            ->assertJsonFragment([
                'name' => 'name',
                'label' => __('global.name'),
                'type' => 'text',
                'value' => null
            ])->assertJsonFragment([
                'name' => 'email',
                'label' => __('global.email'),
                'type' => 'text',
                'value' => null
            ]);
    }

    public function test_guest_cant_store_worker() {
        $this->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_store_worker() {
        $this->actingAs($this->kitchen)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
    }

    public function test_admin_cant_store_worker() {
        $this->actingAs($this->admin)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
    }

    public function test_accountant_cant_store_worker() {
        $this->actingAs($this->accountant)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
    }

    public function test_worker_cant_store_worker() {
        $this->actingAs($this->worker)->post(action('Worker\SupervisorController@storeWorker', $this->workplace))->assertForbidden();
    }

    public function test_supervisor_can_store_worker() {
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@storeWorker',
            $this->workplace),
            [
                'name' => 'name',
                'email' => 'test@test.com',
                'type' => 0,
                'language' => 'en'
            ])->assertSuccessful()
            ->assertJsonFragment([
                'name' => 'name',
                'email' => 'test@test.com',
                'type' => 0,
                'language' => 'en',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'name',
            'email' => 'test@test.com',
            'user_type' => Worker::class
        ]);
    }

    public function test_guest_cant_get_supervisor_datatable() {
        $this->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_admin_cant_get_supervisor_datatable() {
        $this->actingAs($this->admin)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_kitchen_cant_get_supervisor_datatable() {
        $this->actingAs($this->kitchen)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_accountant_cant_get_supervisor_datatable() {
        $this->actingAs($this->accountant)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_worker_cant_get_supervisor_datatable() {
        $this->actingAs($this->worker)->get(action('DatatableController@supervisorList', [

            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_supervisor_can_get_supervisor_datatable() {
        $response = $this->actingAs($this->supervisor)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'WorkersForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertSuccessful();
        $response->assertJsonFragment([
            'name' => $this->worker->name,
            'id' => $this->worker->user->id,
        ]);
    }

    public function test_supervisor_cant_get_workers_datatable_for_other_workplace() {
        $supervisor = User::factory()->make();
        Worker::factory()->create(['supervisor' => true])->user()->save($supervisor);

        $this->actingAs($supervisor)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'WorkersForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_guest_cant_get_worker() {
        $this->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_get_worker() {
        $this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_accountant_cant_get_worker() {
        $this->actingAs($this->accountant)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_admin_cant_get_worker() {
        $this->actingAs($this->admin)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_worker_cant_get_worker() {
        $this->actingAs($this->worker)->get(action('Worker\SupervisorController@editWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_supervisor_can_get_worker() {
        $this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@editWorker', [
            $this->workplace,
            $this->worker->user]))
            ->assertJsonFragment([
                'name' => 'name',
                'label' => __('global.name'),
                'type' => 'text',
                'value' => $this->worker->name,
            ])->assertJsonFragment([
                'name' => 'email',
                'label' => __('global.email'),
                'type' => 'text',
                'value' => $this->worker->email,
            ]);
    }

    public function test_guest_cant_update_worker() {
        $this->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_update_worker() {
        $this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_admin_cant_update_worker() {
        $this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_accountant_cant_update_worker() {
        $this->actingAs($this->accountant)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_worker_cant_update_worker() {
        $this->actingAs($this->worker)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace, $this->worker->user]))->assertForbidden();
    }

    public function test_supervisor_can_update_worker() {
        $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorker', [$this->workplace,
            $this->worker->user]), [
            'name' => 'name',
            'email' => 'test@best.com',
            'type' => 1,
            'language' => 'en',
            'worker' => [
                'data' => 'bata',
            ],
            'workplaces' => [$this->workplace->id],
        ])->assertJsonFragment([
            'name' => 'name',
            'id' => $this->worker->user->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->worker->id,
            'name' => 'name',
            'email' => 'test@best.com',
            'language' => 'en',
            'user_type' => Worker::class,
        ]);

        $this->assertDatabaseHas('workers', [
            'supervisor' => false,
            'type' => 1
        ]);
        $worker = Worker::find($this->worker->user->id);
        $this->assertEquals(collect(['data' => 'bata']), $worker->data);
    }

    public function test_guest_cant_get_shift() {
        $this->get(action('Worker\SupervisorController@editShift', $this->shift))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_get_shift() {
        $this->actingAs($this->kitchen)->get(action('Worker\SupervisorController@editShift',  $this->shift))->assertForbidden();
    }

    public function test_worker_cant_get_shift() {
        $this->actingAs($this->worker)->get(action('Worker\SupervisorController@editShift', $this->shift))->assertForbidden();
    }

    public function test_accountant_cant_get_shift() {
        $this->actingAs($this->accountant)->get(action('Worker\SupervisorController@editShift', $this->shift))->assertForbidden();
    }

    public function test_supervisor_can_get_shift() {
        $response = $this->actingAs($this->supervisor)->get(action('Worker\SupervisorController@editShift', $this->shift))
            ->assertSuccessful();
        $this->assertEquals(collect($response->json()['workers']), $this->workplace->workers()->with('user')->get()->pluck('user.name', 'id')->put(0, ''));
    }

    public function test_admin_can_get_shift() {
        $response = $this->actingAs($this->admin)->get(action('Worker\SupervisorController@editShift', $this->shift))->assertSuccessful()
            ->assertSuccessful();
        $this->assertEquals(collect($response->json()['workers']), $this->workplace->workers()->with('user')->get()->pluck('user.name', 'id')->put(0, ''));
    }

    public function test_guest_cant_close_shift() {
        $this->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_close_shift() {
        $this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertForbidden();
    }

    public function test_accountant_cant_close_shift() {
        $this->actingAs($this->accountant)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertForbidden();
    }

    public function test_worker_cant_close_shift() {
        $this->actingAs($this->worker)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertForbidden();
    }

    public function test_supervisor_can_close_shift() {
        $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertSuccessful();
        $this->assertDatabaseHas('shifts', ['id' => $this->shift->id, 'closed' => true]);
    }

    public function test_admin_can_close_shift() {
        $this->actingAs($this->admin)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertSuccessful();
        $this->assertDatabaseHas('shifts', ['id' => $this->shift->id, 'closed' => true]);
    }

    public function test_supervisor_cant_close_closed_shift() {
        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@closeShift', $this->shift))->assertForbidden();
    }

    public function test_guest_cant_add_worker_to_shift() {
        $this->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_add_worker_to_shift() {
        $this->actingAs($this->kitchen)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_accountant_cant_add_worker_to_shift() {
        $this->actingAs($this->accountant)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_worker_cant_add_worker_to_shift() {
        $this->actingAs($this->worker)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_supervisor_cant_add_unapproved_worker_to_shift() {
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertRedirect()->assertSessionHasErrors(['worker']);

    }

    public function test_supervisor_can_add_approved_worker_to_shift() {
        $this->worker->user->approved = true;
        $this->worker->user->save();
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertSuccessful()
            ->assertJsonFragment([
                'worker' => $this->worker->user->id,
                'startTime' => '20:00',
                'endTime' => '22:00',
                'workFunction' => $this->workplace->workFunctions->first()->id
            ]);
        $this->assertDatabaseHas('shift_worker', [
            'worker_id' => $this->worker->user->id,
            'shift_id' => $this->shift->id,
            'start_time' => '20:00',
            'end_time' => '22:00',
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);
    }

    public function test_supervisor_cant_add_worker_to_closed_shift() {
        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_admin_can_add_unapproved_worker_to_shift() {
        $this->actingAs($this->admin)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertRedirect()->assertSessionHasErrors(['worker']);

    }

    public function test_admin_can_add_approved_worker_to_shift() {
        $this->worker->user->approved = true;
        $this->worker->user->save();
        $this->actingAs($this->admin)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertSuccessful()
            ->assertJsonFragment([
                'worker' => $this->worker->user->id,
                'startTime' => '20:00',
                'endTime' => '22:00',
                'workFunction' => $this->workplace->workFunctions->first()->id
            ]);
        $this->assertDatabaseHas('shift_worker', [
            'worker_id' => $this->worker->user->id,
            'shift_id' => $this->shift->id,
            'start_time' => '20:00',
            'end_time' => '22:00',
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);
    }

    public function test_admin_cant_add_worker_to_closed_shift() {
        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->admin)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_guest_cant_remove_worker_from_shift() {
        $this->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            $this->workplace,
            $this->shift,
            $this->shiftWorker->user
        ]))->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_remove_worker_from_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();
        $this->actingAs($this->kitchen)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertForbidden();
    }

    public function test_accountant_cant_remove_worker_from_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->accountant)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertForbidden();
    }

    public function test_worker_cant_remove_worker_from_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();
        $this->actingAs($this->worker)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertForbidden();
    }

    public function test_supervisor_can_remove_worker_from_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->supervisor)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertSuccessful();
        $this->assertDatabaseMissing('shift_worker', [
            'shift_id' => $this->shift->id,
            'worker_id' => $this->shiftWorker->user->id
        ]);
    }

    public function test_supervisor_cant_remove_worker_from_closed_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->supervisor)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertForbidden();
    }

    public function test_admin_can_remove_worker_from_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->admin)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertSuccessful();
        $this->assertDatabaseMissing('shift_worker', [
            'shift_id' => $this->shift->id,
            'worker_id' => $this->shiftWorker->user->id
        ]);
    }

    public function test_admin_cant_remove_worker_from_closed_shift() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->admin)->delete(action('Worker\SupervisorController@removeWorkerFromShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]))->assertForbidden();
    }

    public function test_guest_cant_update_shift_worker() {
        $this->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $this->shiftWorker->user
        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertRedirect(action('Auth\LoginController@login'));
    }

    public function test_kitchen_cant_update_shift_worker() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->kitchen)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_accountant_cant_update_shift_worker() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->accountant)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id
        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_worker_cant_update_shift_worker() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->worker)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id

        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_supervisor_can_update_shift_worker() {
        $this->shiftWorker->user->approved = true;
        $this->shiftWorker->user->save();
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id

        ]), [
            'worker' => $this->shiftWorker->user->id,
            'startTime' => '18:00',
            'endTime' => '19:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertSuccessful()
            ->assertJsonFragment([
                'worker' => $this->shiftWorker->user->id,
                'startTime' => '18:00',
                'endTime' => '19:00',
                'workFunction' => $this->workplace->workFunctions->first()->id
            ]);
        $this->assertDatabaseHas('shift_worker', [
            'worker_id' => $this->shiftWorker->user->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'shift_id' => $this->shift->id,
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);
    }

    public function test_supervisor_cant_update_closed_shift_worker() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->supervisor)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id

        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_admin_can_update_shift_worker() {
        $this->shiftWorker->user->approved = true;
        $this->shiftWorker->user->save();
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();
        $this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id

        ]), [
            'worker' => $this->shiftWorker->user->id,
            'startTime' => '18:00',
            'endTime' => '19:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertSuccessful()
            ->assertJsonFragment([
                'worker' => $this->shiftWorker->user->id,
                'startTime' => '18:00',
                'endTime' => '19:00',
                'workFunction' => $this->workplace->workFunctions->first()->id
            ]);
        $this->assertDatabaseHas('shift_worker', [
            'worker_id' => $this->shiftWorker->user->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'shift_id' => $this->shift->id,
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);
    }

    public function test_admin_cant_update_closed_shift_worker() {
        $shiftWorker = ShiftWorker::where('shift_id', $this->shift->id)->first();

        $this->shift->closed = true;
        $this->shift->save();
        $this->actingAs($this->admin)->patch(action('Worker\SupervisorController@updateWorkerShift', [
            'shift' => $this->shift,
            'shiftWorker' => $shiftWorker->id

        ]), [
            'startTime' => '20:00',
            'endTime' => '22:00',
            'workFunction' => $this->workplace->workFunctions->first()->id
        ])->assertForbidden();
    }

    public function test_supervisor_can_get_shift_datatable() {
        $response = $this->actingAs($this->supervisor)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'date|asc'
        ]))->assertSuccessful();
        $response->assertJsonFragment([
            'date' => $this->shift->date,
            'id' => $this->shift->id,
            'hours' => $this->shift->hours
        ]);
    }

    public function test_supervisor_cant_get_different_workplace_shift_datatable() {
        $supervisor = User::factory()->make();
        Worker::factory()->create(['supervisor' => true])->user()->save($supervisor);

        $this->actingAs($supervisor)->get(action('DatatableController@supervisorList', [
            'workplace' => $this->workplace,
            'attribute' => 'shiftsForSupervisor',
            'per_page' => 20,
            'sort' => 'name|asc'
        ]))->assertForbidden();
    }

    public function test_hours_overflow_validation() {
        $this->worker->user->approved = true;
        $this->worker->user->save();
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '10:00',
            'endTime' => '22:01',
            'workFunction' => $this->workplace->workFunctions->first()->id])
            ->assertRedirect()->assertSessionHasErrors('endTime');
    }

    public function test_hours_overflow_validation_with_previous_workers() {
        $this->worker->user->approved = true;
        $this->worker->user->save();
        $this->shift->workers()->attach(Worker::factory()->create(), [
            'start_time' => '20:00',
            'end_time' => '23:00',
            'work_function_id' => $this->workplace->workFunctions->first()->id
        ]);
        $this->actingAs($this->supervisor)->post(action('Worker\SupervisorController@addWorkerToShift', $this->shift), [
            'worker' => $this->worker->user->id,
            'startTime' => '21:00',
            'endTime' => '22:30',
            'workFunction' => $this->workplace->workFunctions->first()->id])
            ->assertRedirect()->assertSessionHasErrors('endTime');
    }
}
