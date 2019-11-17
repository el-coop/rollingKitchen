<?php

namespace App\Services;

use App;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\KitchenExportColumn;
use App\Models\Service;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KitchenService implements FromCollection, WithHeadings {
    
    use  Exportable;
    
    public function headings(): array {
        $options = KitchenExportColumn::options();
        if (request()->get('all', false)) {
            return $options->values()->toArray();
        }
        return KitchenExportColumn::orderBy('order')->get()->map(function ($column) use ($options) {
            return $options[$column->column];
        })->toArray();
    }
    
    
    public function collection() {
        $kitchens = Kitchen::whereHas('applications', function ($query) {
            $query->where([['status', '=', 'accepted'], ['year', '=', app('settings')->get('registration_year')]]);
        })->get();
        if (request()->get('all', false)) {
            $fields = KitchenExportColumn::options()->keys();
        } else {
            $fields = KitchenExportColumn::orderBy('order')->get()->pluck('column');
        }
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
                    $result->push($kitchen->data[$column] ?? '');
                    break;
                case 'service':
                    $application = $kitchen->getCurrentApplication();
                    $service = $application->services->find($column);
                    $result->push($service->pivot->quantity ?? 0);
                    break;
                case 'application':
                    $application = $kitchen->getCurrentApplication();
                    if (is_numeric($column)) {
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
