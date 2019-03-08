<?php

namespace App\Http\Requests\ArtistManager;

use App\Models\Band;
use Illuminate\Foundation\Http\FormRequest;

class StoreBandScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('schedule',Band::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        	'calendar' => 'required|array',
			'calendar.*' => 'required|array',
			'calendar.*.*' => 'array',
			'calendar.*.*.band' => 'required|exists:bands,id',
			'calendar.*.*.payment' => 'required|min:1',
			'calendar.*.*.stage' => 'required|exists:stages,id',
        ];
    }
	
	public function commit() {
	
	}
}
