<?php

namespace App\Services;

use App;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\KitchenExportColumn;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KitchenService implements FromCollection, WithHeadings {

	use  Exportable;

	public function headings(): array {
		return KitchenExportColumn::orderBy('order')->get()->pluck('name')->toArray();
	}


	public function collection() {
		$kitchens = Kitchen::whereHas('applications', function ($query) {
			$query->where([['status','=','accepted'], ['year','=', app('settings')->get('registration_year')]]);
		})->get();
		$fields = KitchenExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($kitchens as $kitchen) {
			$data->push($this->listData($fields, $kitchen));
		}
		return $data;
	}

	/**
	 * @param $fields
	 * @param $kitchen
	 * @return \Illuminate\Support\Collection
	 */
	protected function listData($fields, Kitchen $kitchen): \Illuminate\Support\Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'kitchen':
					$column = Field::find($column)->id;
					$result->push($kitchen->data[$column] ?? '');
					break;
				case 'services':
					$application = $kitchen->getCurrentApplication();
					$services = $application->services->count();
					$result->push($services);
					break;
				default:
					$result->push($kitchen->user->$column);

			}
		}
		return $result;
	}
}
