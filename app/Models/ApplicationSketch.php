<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ApplicationSketch extends Model {
    use HasFactory;

    protected static function boot() {
        parent::boot();
        static::deleted(function ($photo) {
            \Storage::delete("public/photos/{$photo->file}");
        });
    }

    public $appends = [
        'url',
    ];


    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Application::class);
    }

    public function getUrlAttribute() {
        return action('PhotoController@applicationSketch', $this);
    }
}
