<?php

namespace App\Http\Requests\Admin\Fields;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFieldRequest extends FormRequest {
    protected $field;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $this->field = $this->route('field');

        return $this->user()->can('delete', $this->field);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            //
        ];
    }

    public function commit() {
        $this->field->delete();
    }
}
