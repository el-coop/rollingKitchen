<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Models\Kitchen;
use App\Models\KitchenExportColumn;
use App\Models\Service;
use App\Models\User;
use App\Services\KitchenService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KitchenServiceTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	protected $kitchens;
	protected $services;
	protected $kitchenColumns;
	protected $kitchenService;

	protected function setUp(): void {
		parent::setUp();
		$applicationYear = app('settings')->get('registration_year');

		$this->services = factory(Service::class, 10)->create();
		$this->kitchens = factory(Kitchen::class, 5)->create()->each(function ($kitchen) use ($applicationYear) {
			$kitchen->user()->save(factory(User::class)->make());
			$application = factory(Application::class)->make(['year' => $applicationYear]);
			$kitchen->applications()->save($application);
			Service::inRandomOrder()->limit(3)->get()->each(function ($service) use ($application) {
				$application->services()->save($service, ['quantity' => random_int(1, 5)]);
			});
		});
		$columns = ['user.name', 'user.email', 'application.year', 'service.' . $this->services->random()->id];
		$i = -1;
		$this->kitchenColumns = factory(KitchenExportColumn::class, 4)->make()->each(function ($column) use ($columns,$i){
			$i = $i + 1;
			$column->column = $columns[$i];
			$column->orded = $i;
		});
		$this->kitchenService = new KitchenService;

	}

	public function test_sets_heading() {
		$headings = $this->kitchenService->headings();
		$expectedHeadings = KitchenExportColumn::orderBy('order')->get()->pluck('name')->toArray();
		$this->assertEquals($expectedHeadings, $headings);
	}

	public function test_collection() {
		$collection = $this->kitchenService->collection();
		$data = $this->collect();
		$this->assertEquals($data, $collection);
	}
	private function collect() {
		$kitchens = Kitchen::whereHas('applications', function ($query) {
			$query->where([['status','=','accepted'], ['year','=', app('settings')->get('registration_year')]]);
		})->get();
		$fields = KitchenExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($kitchens as $kitchen) {
			$data->push($this->getData($fields, $kitchen));
		}
		return $data;
	}

	private function getData($fields,$kitchen): \Illuminate\Support\Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'kitchen':
					$result->push($kitchen->data[$column] ?? '');
					break;
				case 'service':
					$application = $kitchen->getCurrentApplication();
					$service = $application->services->find($column);
					$result->push($service->pivot->quantity ?? 0);
					break;
				case 'application':
					$application = $kitchen->getCurrentApplication();
					if (is_numeric($column)){
						$result->push($application->data[$column] ?? '');
					} else {
						$result->push($application->$column);
					}
					break;
				default:
					$result->push($kitchen->user->$column);
					break;
			}
		}
		return $result;
	}
}
