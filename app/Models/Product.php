<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;

    public function application() {
		return $this->belongsTo(Application::class);
	}

    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(ProductPhoto::class);
    }
}
