<?php

namespace Tests\Unit;

use App\Models\Shift;
use App\Models\ShiftWorker;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Services\WorkedHoursService;
use App\Services\WorkplaceShiftsExportService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupervisorExportShiftsTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $shifts;
	protected $workplace;
	/**
	 * @var WorkplaceShiftsExportService
	 */
	private $exportShiftsService;
	
	protected function setUp(): void {
		parent::setUp();
		$this->workplace = factory(Workplace::class)->create();
		$workFunction = factory(WorkFunction::class)->create([
			'workplace_id' => $this->workplace->id
		]);
		$this->shifts = factory(Shift::class, 2)->create(['workplace_id' => $this->workplace->id]);
		
		$this->shifts->each(function ($shift) use ($workFunction) {
			factory(Worker::class, 2)->create()->each(function ($worker) use ($shift, $workFunction) {
				$worker->user()->save(factory(User::class)->make());
				$shift->workers()->attach($worker, [
					'work_function_id' => $workFunction->id,
					'start_time' => '10:00',
					'end_time' => '11:00',
				]);
			});
		});
		
		$this->exportShiftsService = new WorkplaceShiftsExportService($this->shifts);
	}
	
	public function test_sets_heading() {
		$headings = $this->exportShiftsService->headings();
		$this->assertEquals([__('worker/worker.workplace'), __('admin/shifts.date'), __('worker/supervisor.workFunction'), __('global.name'), __('admin/shifts.startTime'), __('admin/shifts.endTime')], $headings);
	}
	
	public function test_collection() {
		$collection = $this->exportShiftsService->collection();
		$data = $this->collect();
		$this->assertEquals($data, $collection);
	}
	
	protected function collect() {
		$data = collect();
		foreach ($this->shifts as $shift) {
			foreach ($shift->shiftWorkers as $worker) {
				$data->push([
					$shift->workplace->name,
					Carbon::createFromFormat('Y-m-d', $shift->date)->format('d/m/Y'),
					$worker->workFunction->name,
					$worker->worker->user->name ?? '',
					$worker->start_time,
					$worker->end_time,
				]);
			}
			$data->push(['', '', '', '']);
		}
		return $data;
	}
}
