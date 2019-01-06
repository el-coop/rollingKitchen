<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model {

	protected $casts = [
		'default_send_invoice' => 'boolean',
		'default_resend_invoice' => 'boolean'
	];

    protected static function boot() {
        parent::boot();
        static::deleted(function ($pdf) {
            \Storage::delete("public/pdf/{$pdf->file}");
        });
    }
}
