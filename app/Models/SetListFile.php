<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetListFile extends Model {

    protected static function boot() {
        parent::boot();
        static::deleted(function ($setlist) {
            \Storage::delete("public/pdf/band/{$setlist->file}");
        });
    }

    public function band() {
        return $this->belongsTo(Band::class);
    }
}
