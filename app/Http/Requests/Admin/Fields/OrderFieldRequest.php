<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;

class OrderFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'order' => 'required|array'
        ];
    }

    public function commit(){
        $newOrder = $this->input('order');
        for($i = 1; $i <= count($newOrder); $i++){
            $field = Field::find($newOrder[$i-1]);
            $field->order = $i;
            $field->save();
        }
    }
}
