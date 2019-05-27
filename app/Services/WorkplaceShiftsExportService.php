<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkplaceShiftsExportService implements FromCollection, WithHeadings {
	
	use  Exportable;
	
	private $shifts;
	
	public function __construct($shifts) {
		$this->shifts = $shifts;
	}
	
	public function headings(): array {
		return [__('worker/worker.workplace'), __('admin/shifts.date'), __('worker/supervisor.workFunction'), __('global.name'), __('admin/shifts.startTime'), __('admin/shifts.endTime')];
	}
	
	public function collection() {
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
