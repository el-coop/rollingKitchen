<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFunction extends Model {
    use HasFactory;

	public function workplace(){
		return $this->belongsTo(Workplace::class);
	}
}
