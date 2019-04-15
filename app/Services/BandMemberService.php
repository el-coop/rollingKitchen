<?php

namespace App\Services;

use App;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandMemberExportColumn;
use App\Models\Field;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BandMemberService implements FromCollection, WithHeadings {
	
	use  Exportable;
	
	public function headings(): array {
		return BandMemberExportColumn::orderBy('order')->get()->pluck('name')->toArray();
	}
	
	
	public function collection() {
		$bandMembers = BandMember::whereHas('band', function ($query) {
			$query->where('payment_method', 'individual')->whereHas('schedules', function ($query) {
				$query->where('approved', 'accepted');
			});
		})->orderBy('band_id')->get();
		$fields = BandMemberExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($bandMembers as $bandMember) {
			$data->push($this->listData($fields, $bandMember));
		}
		return $data;
	}
	
	/**
	 * @param $fields
	 * @param $bandMember
	 * @return \Illuminate\Support\Collection
	 */
	protected function listData($fields, BandMember $bandMember): Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'band':
					$result->push($bandMember->band->user->name);
					break;
				case 'bandMember':
					switch ($column) {
						case 'payment':
							$result->push($bandMember->payment);
							break;
						case 'pdf':
							$result->push(action('Admin\BandMemberController@pdf', $bandMember));
							break;
						default:
							$result->push($bandMember->data[$column] ?? '');
						
					}
					break;
				default:
					$result->push($bandMember->user->$column);
					break;
			}
		}
		return $result;
	}
	
	public function individual(BandMember $bandMember) {
		$fields = BandMemberExportColumn::orderBy('order')->get();
		return $fields->pluck('name')->combine($this->listData($fields->pluck('column'), $bandMember));
	}
}
