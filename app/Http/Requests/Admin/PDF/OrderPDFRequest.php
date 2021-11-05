<?php

namespace App\Http\Requests\Admin\PDF;

use App\Models\Pdf;
use Illuminate\Foundation\Http\FormRequest;

class OrderPDFRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->can('order', Pdf::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'order' => 'required|array'
        ];
    }

    public function commit() {
        $newOrder = $this->input('order');
        for ($i = 1; $i <= count($newOrder); $i++) {
            $pdf = Pdf::find($newOrder[$i - 1]);
            $pdf->order = $i;
            $pdf->save();
        }
    }
}
