<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 12/11/2018
 * Time: 17:01
 */

namespace App\Models\Traits;


use Illuminate\Support\Facades\DB;

trait GetLastFieldOrder {
    static function getLastFieldOrder(){
        return DB::table('fields')->where('form', '=',static::class)->max('order');
    }
}
