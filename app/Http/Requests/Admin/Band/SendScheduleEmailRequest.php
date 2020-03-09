<?php

namespace App\Http\Requests\Admin\Band;

use App\Models\Band;
use Illuminate\Foundation\Http\FormRequest;

class SendScheduleEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('sendConfirmation', Band::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'bands' => 'array'
        ];
    }

    public function commit(){
        $bands = Band::findMany(array_keys($this->input('bands')));
        $bands->each(function ($band){
            $schedules = $band->schedules;

        });
    }
}
