<?php

namespace Tests\Unit;

use App\Models\Shift;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Services\WorkedHoursService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkedHoursServiceTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $shifts;
	protected $workplaces;
	protected $workedHoursColumns;
	protected $workedHoursService;

	protected function setUp(): void {
		parent::setUp();
		$this->workplaces = Workplace::factory(10)->create()->each(function ($workplace) {
			$workfunction = WorkFunction::factory()->make();
			$workplace->workFunctions()->save($workfunction);
		});
		$this->shifts = Shift::factory(6)->make([
			'date' => $this->faker->dateTimeBetween(Carbon::parse('first day of January'), Carbon::parse('last day of december'))
		])->each(function ($shift) {
			$shift->workplace_id = $this->workplaces->random()->id;
			$shift->closed = true;
			$shift->save();
			User::factory(3)->make()->each(function ($user) use ($shift) {
				$worker = Worker::factory()->create()->user()->save($user);
				$worker->user->workplaces()->attach($shift->workplace);
				$shift->workers()->attach($worker, ['start_time' => '10:00', 'end_time' => '20:00', 'work_function_id' => $shift->workplace->workFunctions->first()->id]);
			});
		});
		$columns = ['user.name', 'shift.workplace', 'worker.type', 'shift_worker.start_time', 'shift_worker.end_time', 'shift_worker.work_function_id'];
		$i = -1;
		$this->workedHoursColumns = WorkedHoursExportColumn::factory(6)->make()->each(function ($workedHoursColumn) use ($i, $columns) {
			$i = $i + 1;
			$workedHoursColumn->column = $columns[$i];
			$workedHoursColumn->orded = $i;
		});
		$this->workedHoursService = new WorkedHoursService;
	}

	public function test_sets_heading() {
		$headings = $this->workedHoursService->headings();
		$expectedHeadings = WorkedHoursExportColumn::orderBy('order')->get()->pluck('name')->toArray();
		$this->assertEquals($expectedHeadings, $headings);
	}

	public function test_collection() {
		$collection = $this->workedHoursService->collection();
		$data = $this->collect();
		$this->assertEquals($data, $collection);
	}

	public function test_individual() {
		$worker = Worker::first();
		$individual = $this->workedHoursService->individual($worker);
		$fields = WorkedHoursExportColumn::where('column', 'NOT LIKE', 'shift%')->orderBy('order')->get();
		$data = $fields->pluck('name')->combine($this->getData($fields->pluck('column'), $worker));
		$this->assertEquals($data, collect($individual->toArray()));
	}

	private function collect() {
		$shifts = Shift::where('closed', true)->where('date', '>', Carbon::parse('first day of January'))->get();
		$fields = WorkedHoursExportColumn::orderBy('order')->get()->pluck('column')->toArray();
		$data = collect();
		foreach ($shifts as $shift) {
			foreach ($shift->workers as $worker) {
				$data->push($this->getData($fields, $shift, $worker));
			}
		}
		return $data;
	}

	/**
	 * @param $fields
	 * @param $shift
	 * @param $worker
	 * @return \Illuminate\Support\Collection
	 */
	private function getData($fields, $worker, $shift = null): \Illuminate\Support\Collection {
		$workedHourRow = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'shift':
					if ($column == 'workplace_id') {
						$workedHourRow->push($shift->workplace->name);

					} else {
						$workedHourRow->push($shift->$column);
					}
					break;
				case 'worker':
					if ($column == 'type') {
						$workedHourRow->push($worker->type);

					} else {
						$column = Field::find($column)->id;
						$workedHourRow->push($worker->data[$column]);
					}
					break;
				case 'shift_worker':
					$pivot = $worker->shifts->find($shift);
					if ($column == 'work_function_id') {
						$workedHourRow->push(WorkFunction::find($pivot->pivot->work_function_id)->name);
					} else {
						$workedHourRow->push($pivot->pivot->$column);
					}
					break;
				case 'user':
					$workedHourRow->push($worker->user->$column);
					break;
			}
		}
		return $workedHourRow;
	}
}
