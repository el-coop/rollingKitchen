<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 08/11/2018
 * Time: 15:29
 */

namespace App\Models\Traits;


use App\Models\Field;

trait HasFields {
    static function fields(){
        return Field::where('form',static::class)->orderBy('order')->get();
    }

}