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
	
	public function collection() {
		$shifts = Shift::where('closed', true)->get();
		$fields = WorkedHoursExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($shifts as $shift) {
			foreach ($shift->workers as $worker) {
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
								switch ($worker->type) {
									case 0:
										$workedHourRow->push(__('admin/workers.payroll'));
										break;
									case 1:
										$workedHourRow->push(__('admin/workers.freelance'));
										break;
									default:
										$workedHourRow->push(__('admin/workers.volunteer'));
								}
								
							} else {
								$column = Field::find($column)->id;
								$workedHourRow->push($worker->data[$column] ?? '');
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
				$data->push($workedHourRow);
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
}
