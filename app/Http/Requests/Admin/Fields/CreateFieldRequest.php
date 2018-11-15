<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Field;
use App\Models\Kitchen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFieldRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->can('create', Field::class);
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
            'form' => 'required|string|in:' . Kitchen::class,
            'options' => 'required_if:type,checkbox|array',
            'name_nl' => 'required:string'

        ];
    }

    public function commit(){

        $field = new Field;
        $field->form = $this->input('form');
        $field->name = $this->input('name');
        $field->type = $this->input('type');
        $field->name_nl = $this->input('name_nl');
        // maybe we implement some kind of switch case or a function that will do that when we have more model fields
        $field->order = Kitchen::getLastFieldOrder() + 1;
        if ($field->type == 'checkbox') {
            $field->options = $this->input('options');
        }
        $field->save();
        return $field;
    }
}
