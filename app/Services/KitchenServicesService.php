<?php

namespace App\Services;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KitchenServicesService implements FromCollection, WithHeadings{

    use  Exportable;

    public function headings(): array {
        $services = Service::all()->pluck('name_' . app()->getLocale());
        return collect(['name'])->concat($services)->toArray();
    }
    public function collection() {
        $services = Service::all();
        $kitchens = User::where('user_type', Kitchen::class)->get();
        $data = collect();
        foreach ($kitchens as $kitchen) {
            $kitchenRow = collect();
            $kitchenRow->push($kitchen->name);
            $application = $kitchen->user->getCurrentApplication();
            foreach ($services as $service){
                $column = 0;

                if ($application->hasService($service)){
                    $column = $application->serviceQuantity($service);
                }
                $kitchenRow->push($column);
            }
            $data->push($kitchenRow);
            }
        return $data;
    }
}
