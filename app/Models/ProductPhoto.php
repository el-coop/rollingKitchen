<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class ProductPhoto extends Model {
    use HasFactory;

    public $appends = [
        'url',
    ];

    protected static function boot() {
        parent::boot();
        static::deleted(function ($photo) {
            Storage::delete("public/photos/{$photo->file}");
        });
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute() {
        return action('PhotoController@productPhoto', $this);
    }
}
