<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandPdf extends Model {
    use HasFactory;

	protected static function boot() {
		parent::boot();
		static::deleted(function ($pdf) {
			\Storage::delete("public/pdf/band/{$pdf->file}");
		});
	}

	public function band() {
		return $this->belongsTo(Band::class);
	}
}
