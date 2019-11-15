<?php

namespace App\Services;

use App;
use App\Models\Band;
use App\Models\BandPaymentExportColumn;
use App\Models\Field;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BandPaymentService implements FromCollection, WithHeadings {

	use  Exportable;

	public function headings(): array {
        $options = BandPaymentExportColumn::options();
        return BandPaymentExportColumn::orderBy('order')->get()->map(function ($column) use ($options) {
            return $options[$column->column];
        })->toArray();
	}


	public function collection() {
		$bands = Band::whereHas('schedules', function ($query) {
			$query->where('approved', 'accepted');
		})->get();
		$fields = BandPaymentExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($bands as $band) {
			$data->push($this->listData($fields, $band));
		}
		return $data;
	}

	/**
	 * @param $fields
	 * @param $band
	 * @return \Illuminate\Support\Collection
	 */
	protected function listData($fields, Band $band): \Illuminate\Support\Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			if ($model == 'band') {
				if ($column == 'totalPayment') {
					$result->push($band->approvedPayments);

				} else {
					$result->push($band->data[$column] ?? '');
				}
			} else {
				$result->push($band->user->$column);
			}
		}
		return $result;
	}
}
