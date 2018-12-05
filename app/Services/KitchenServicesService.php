<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KitchenServicesService implements FromCollection, WithHeadings {
	
	use  Exportable;
	
	public function headings(): array {
		$services = Service::all()->pluck('name_' . app()->getLocale());
		return collect([__('admin/applications.number'), __('auth.kitchenName')])->concat($services)->toArray();
	}
	
	public function collection() {
		$applicationYear = app('settings')->get('registration_year');
		$services = Service::all();
		
		$applications = Application::where('year', $applicationYear)->where('status','accepted')->with('kitchen.user', 'services')->get();
		$data = collect();
		foreach ($applications as $application) {
			$kitchenRow = collect();
			$kitchenRow->push($application->number);
			$kitchenRow->push($application->kitchen->user->name);
			foreach ($services as $service) {
				$column = 0;
				
				if ($application->hasService($service)) {
					$column = $application->serviceQuantity($service);
				}
				$kitchenRow->push($column);
			}
			$data->push($kitchenRow);
		}
		return $data;
	}
}
