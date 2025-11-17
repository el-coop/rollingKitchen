<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;

    protected $appends = [
      'photosJson'
    ];

    public function application() {
        return $this->belongsTo(Application::class);
    }

    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(ProductPhoto::class);
    }

    public function getFullDataAttribute() {
        return collect([
            [
                'name' => 'name',
                'label' => __('admin/applications.product'),
                'type' => 'text',
                'value' => $this->name
            ],
            [
                'name' => 'price',
                'label' => __('admin/applications.price'),
                'type' => 'text',
                'subType' => 'number',
                'value' => $this->price,
            ],
            [
                'name' => 'category',
                'type' => 'hidden',
                'value' => 'menu'
            ],
            [
                'name' => 'photosJson',
                'noTable' => true,
                'visible' => false,
                'value' => $this->photos
            ]
        ]);
    }

    public function getPhotosJsonAttribute() {
        return $this->photos->toJson();
    }
}
