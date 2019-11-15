<?php

namespace App\Services;

use App;
use App\Models\Band;
use App\Models\BandAdmin;
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
        $options = BandMemberExportColumn::options();
        return BandMemberExportColumn::orderBy('order')->get()->map(function ($column) use ($options) {
            return $options[$column->column];
        })->toArray();
    }
    
    
    public function collection() {
        $bands = Band::where('payment_method', 'individual')->whereHas('schedules', function ($query) {
            $query->where('approved', 'accepted');
        })->get();
        $fields = BandMemberExportColumn::orderBy('order')->get()->pluck('column');
        $data = collect();
        foreach ($bands as $band) {
            $data->push($this->listAdminData($fields, $band->admin));
            foreach ($band->bandMembers as $bandMember) {
                $data->push($this->listData($fields, $bandMember));
            }
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
    
    public function listAdminData($fields, BandAdmin $bandAdmin) {
        $result = collect();
        foreach ($fields as $field) {
            $model = strtok($field, '.');
            $column = strtok('.');
            switch ($model) {
                case 'band':
                    $result->push($bandAdmin->band->user->name);
                    break;
                case 'bandMember':
                    switch ($column) {
                        case 'payment':
                            $result->push($bandAdmin->payment);
                            break;
                        case 'pdf':
                            $result->push(action('Admin\BandController@adminPdf', $bandAdmin));
                            break;
                        default:
                            $result->push($bandAdmin->data[$column] ?? '');
                        
                    }
                    break;
                default:
                    if ($model == 'name') {
                        $result->push($bandAdmin->name);
                    } else {
                        $result->push($bandAdmin->band->user->email);
                    }
                    break;
            }
        }
        return $result;
    }
    
    public function adminIndividual(BandAdmin $bandAdmin) {
        $fields = BandMemberExportColumn::orderBy('order')->get();
        return $fields->pluck('name')->combine($this->listAdminData($fields->pluck('column'), $bandAdmin));
    }
}
