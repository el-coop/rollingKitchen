<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;

class CreateFieldRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string',
            'type' => 'required|string|in:text,textarea,checkbox',
            'checkbox_options' => 'required_if:type,checkbox|json'

        ];
    }

    public function commit(){
        $field = new Field;
        $field->name = $this->input('name');
        $field->type = $this->input('type');
        if ($field->type == 'checkbox') {
            $field->json = $this->input('checkbox_options');
        }
        $field->save();
        return $field;
    }
}
