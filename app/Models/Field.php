<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model {
    use HasFactory;

    protected $casts = [
        'options' => 'array',
        'condition_value' => 'array'
    ];

    static function getRequiredFields(...$models) {
        $requiredFields = Field::where('status', 'required')->whereIn('form', $models)->get();
        return $requiredFields->mapWithKeys(function ($field) use (&$rules) {
            $dataName = strtolower(substr($field->form, strrpos($field->form, '\\') + 1));
            return ["{$dataName}.{$field->id}" => 'required'];
        });
    }

    static function getProtectedFields(...$models) {
        $requiredFields = Field::where('status', 'protected')->whereIn('form', $models)->get();
        return $requiredFields->mapWithKeys(function ($field) use (&$rules) {
            $dataName = strtolower(substr($field->form, strrpos($field->form, '\\') + 1));
            return ["{$dataName}.{$field->id}" => 'required'];
        });
    }
}

