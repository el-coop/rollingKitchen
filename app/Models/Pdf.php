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

    public static function allForInvoice($exists){
    	$options = [];
    	$pdfs = Pdf::all();
		foreach ($pdfs as $pdf) {
			$checked = false;
			if ($exists){
				if ($pdf->default_send_invoice == true){
					$checked = true;
				}
			} else {
				if ($pdf->default_resend_invoice == true){
					$checked = true;
				}
			}

			$options[$pdf->id] = ['name' => $pdf->name, 'checked' => $checked];
    	}
    	return $options;
	}
}
