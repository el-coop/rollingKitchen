<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['name', 'value'];

    public static function all($columns = ['*']) {
        $settings = parent::all($columns);
        $settings->push(Setting::registrationYear());
        return $settings; // TODO: Change the autogenerated stub
    }

    public static function registrationYear(){
        if (today() > Carbon::create(date('Y'), 12,15)){
            $year = date('Y') + 1;
        } else {
            $year = date('Y');
        }
        return  new Setting(['name' => 'registration_year', 'value' => $year]);
    }
}
