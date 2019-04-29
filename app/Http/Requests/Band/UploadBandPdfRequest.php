<?php

namespace App\Http\Requests\Band;

use App\Models\BandPdf;
use Illuminate\Foundation\Http\FormRequest;

class UploadBandPdfRequest extends FormRequest {
	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('update', $this->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'file' => 'required|file'
		];
	}

	public function commit() {
		$pdf = new BandPdf;
		$path = $this->file('file')->store('public/pdf/band');
		$pdf->file = basename($path);
		if ($this->band->pdf){
			$this->band->pdf->delete();
		}

		$this->band->pdf()->save($pdf);
		return $pdf;
	}
}
