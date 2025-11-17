<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    use HasFactory;

    protected static function boot() {
        parent::boot();
        static::deleted(function ($service) {
            $service->applications()->detach();
        });
    }

    protected $casts = [
        'conditions' => 'array'
    ];

    public function getFullDataAttribute() {

        $fullData = collect([[
            'name' => 'name_nl',
            'label' => __('admin/fields.name_nl'),
            'type' => 'text',
            'value' => $this->name_nl,
        ], [
            'name' => 'name_en',
            'label' => __('admin/fields.name_en'),
            'type' => 'text',
            'value' => $this->name_en,
        ], [
            'name' => 'category',
            'label' => __('admin/services.category'),
            'type' => 'select',
            'options' => [
                'socket' => __('vue.socket'),
                'safety' => __('vue.safety'),
                'electrical' => __('vue.electrical'),
                'misc' => __('vue.misc'),
            ],
            'value' => $this->category,
        ], [
            'name' => 'price',
            'label' => __('admin/applications.price'),
            'type' => 'text',
            'subType' => 'number',
            'value' => $this->price,
        ], [
            'name' => 'type',
            'label' => __('admin/fields.type'),
            'type' => 'serviceType',
            'options' => [
                __('admin/services.amount'),
                __('admin/services.select'),
                __('admin/services.scale'),
                __('admin/services.equivalent'),
            ],
            'value' => $this->type,
            'subValue' => $this->conditions
        ], [
            'name' => 'mandatory',
            'type' => 'checkbox',
            'value' => $this->mandatory,
            'options' => [[
                'name' => __('admin/services.mandatory'),
            ]]
        ]]);
        return $fullData;
    }

    public function applications() {
        return $this->belongsToMany(Application::class)->withPivot('quantity', 'equivalent_price')->withTimestamps();
    }

    public function applicationEquivalentPrice(Application $application) {
        return (float)$this->applications()->where('application_id', $application->id)->first()->pivot->equivalent_price;
    }

}
