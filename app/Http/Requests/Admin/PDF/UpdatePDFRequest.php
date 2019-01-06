<?php

namespace App\Http\Requests\Admin\PDF;

use App\Models\Pdf;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePDFRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->pdf = $this->route('pdf');
		return $this->user()->can('update', $this->pdf);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|unique:pdfs,name,' . $this->pdf->id,
			'visibility' => 'required|in:0,1,2'
		];
	}

	public function commit() {
		$this->pdf->name = $this->input('name');
		$this->pdf->visibility = $this->input('visibility');
		$this->pdf->default_send_invoice = $this->has('default_send_invoice');
		$this->pdf->default_resend_invoice = $this->has('default_resend_invoice');
		$this->pdf->save();
		return $this->pdf;
	}
}
