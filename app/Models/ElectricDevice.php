<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricDevice extends Model
{
	public function application() {
		return $this->belongsTo(Application::class);
    }
}
