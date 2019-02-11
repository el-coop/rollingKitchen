<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Shift;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use App\Models\WorkFunction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkedHoursService implements FromCollection, WithHeadings {
	
	use  Exportable;
	
	public function headings(): array {
		return WorkedHoursExportColumn::orderBy('order')->get()->pluck('name')->toArray();
	}
	
	public function individual(Worker $worker) {
		$fields = WorkedHoursExportColumn::where('column', 'NOT LIKE', 'shift%')->orderBy('order')->get();
		return $fields->pluck('name')->combine($this->listData($fields->pluck('column'), $worker));
		
	}
	
	public function collection() {
		$shifts = Shift::where('closed', true)->with('workers.user')->get();
		$fields = WorkedHoursExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($shifts as $shift) {
			foreach ($shift->workers as $worker) {
				$data->push($this->listData($fields, $worker, $shift));
			}
		}
		$namesIndex = $fields->search(function ($item) {
			return $item === 'user.name';
		});
		$dateIndex = $fields->search(function ($item) {
			return $item === 'shift.date';
		});
		return $data->sortBy(function ($item) use ($namesIndex, $dateIndex) {
			if ($namesIndex !== false && $dateIndex !== false) {
				return [$item[$namesIndex], new Carbon($item[$dateIndex])];
			}
			if ($namesIndex !== false) {
				return $item[$namesIndex];
			}
			if ($dateIndex !== false) {
				return $item[$dateIndex];
			}
			return $item;
			
		});
	}
	
	/**
	 * @param $fields
	 * @param $shift
	 * @param $worker
	 * @return \Illuminate\Support\Collection
	 */
	protected function listData($fields, Worker $worker, ?Shift $shift = null): \Illuminate\Support\Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'shift':
					if ($column == 'workplace_id') {
						$result->push($shift->workplace->name);
						
					} else {
						$result->push($shift->$column);
					}
					break;
				case 'worker':
					switch ($column) {
						case 'type':
							switch ($worker->type) {
								case 0:
									$result->push(__('admin/workers.payroll'));
									break;
								case 1:
									$result->push(__('admin/workers.freelance'));
									break;
								default:
									$result->push(__('admin/workers.volunteer'));
									break;
							}
							break;
            case 'pdf':
                $workedHourRow->push(action('Admin\WorkerController@pdf', $worker));
                break;
            case 'workedHours':
							$result->push($worker->workedHours);
							break;
						default:
							$column = Field::find($column)->id;
							$result->push($worker->data[$column] ?? '');
							break;
					}
					break;
				case 'shift_worker':
					$pivot = $worker->shifts->find($shift);
					if ($column == 'work_function_id') {
						$result->push(WorkFunction::find($pivot->pivot->work_function_id)->name);
					} else {
						$result->push($pivot->pivot->$column);
					}
					break;
				case 'user':
					$result->push($worker->user->$column);
					break;
			}
		}
		return $result;
	}
}
