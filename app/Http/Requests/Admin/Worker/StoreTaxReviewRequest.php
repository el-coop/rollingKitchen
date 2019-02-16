<?php

namespace App\Http\Requests\Admin\Worker;

use App\Events\Worker\TaxReviewUploaded;
use App\Models\TaxReview;
use Illuminate\Foundation\Http\FormRequest;
use Storage;

class StoreTaxReviewRequest extends FormRequest {
	private $worker;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->worker = $this->route('worker');
		return $this->user()->can('update', $this->worker);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'file' => 'file|required',
			'name' => 'required'
		];
	}
	
	public function commit() {
		$file = $this->file('file');
		$path = "public/taxReviews/{$file->hashName()}";
		Storage::put($path, encrypt(file_get_contents($file->getRealPath())));
		
		$taxReview = new TaxReview();
		$taxReview->file = basename($path);
		$taxReview->name = $this->input('name');
		$this->worker->taxReviews()->save($taxReview);
		
		event(new TaxReviewUploaded($this->worker));
		
		return $taxReview;
	}
	
}
