<?php

namespace App\Http\Requests\Admin\PDF;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class DeletePDFRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    protected $pdf;
    public function authorize() {
        $this->pdf = $this->route('pdf');
        return $this->user()->can('delete', $this->pdf);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
        ];
    }

    public function commit(){
        $this->pdf->delete();
    }
}
