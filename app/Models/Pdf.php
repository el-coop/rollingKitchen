<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model {

	protected $casts = [
		'default_send_invoice' => 'boolean',
		'default_resend_invoice' => 'boolean',
	];

	public static function allForInvoice($exists) {
		$pdfs = Pdf::all()->mapWithKeys(function ($pdf) use ($exists) {
			if (! $exists) {
				$checked = $pdf->default_send_invoice;
			} else {
				$checked = $pdf->default_resend_invoice;
			}

			return [$pdf->id => ['name' => $pdf->name, 'checked' => $checked]];
		});
		return $pdfs;
	}

	protected static function boot() {
		parent::boot();
		static::deleted(function ($pdf) {
			\Storage::delete("public/pdf/{$pdf->file}");
		});
	}
}
