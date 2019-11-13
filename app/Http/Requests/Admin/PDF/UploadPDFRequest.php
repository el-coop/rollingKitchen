<?php

namespace App\Http\Requests\Admin\PDF;

use App\Models\Pdf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class UploadPDFRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Pdf::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'file' => 'required|file|clamav',
			'name' => 'required|string|unique:pdfs',
			'visibility' => 'required|in:0,1,2,3'
		];
	}

	public function commit() {
		$path = $this->file('file')->store('public/pdf');
		$pdf = new Pdf;
		$pdf->file = basename($path);
		$pdf->name = $this->input('name');
		$pdf->visibility = $this->input('visibility');
		$pdf->default_send_invoice = $this->has('default_send_invoice');
		$pdf->default_resend_invoice = $this->has('default_resend_invoice');
		$pdf->save();
		return $pdf;
	}
}
