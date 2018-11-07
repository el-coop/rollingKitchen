<?php

namespace App\Http\Requests\Fields;

use Illuminate\Foundation\Http\FormRequest;

class EditFieldRequest extends FormRequest
{
    protected $field;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->field = $this->route('field');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:fields',
            'type' => 'required|string|in:text,textarea,checkbox',
            'checkbox_options' => 'required_if:type,checkbox|json'
        ];
    }

    public function edit(){
        $this->field->name = $this->input('name');
        $this->field->type = $this->input('type');
        if ($this->field->type == 'checkbox') {
            $this->field->json = $this->input('checkbox_options');
        }
        $this->field->save();
        return $this->field;

    }
}
