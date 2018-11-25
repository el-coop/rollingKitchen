<?php

namespace App\Http\Requests\Admin\Settings;

use App\Models\Setting;
use Dotenv\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateSettingsRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->can('update', Setting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
            return [
                'accountant' => 'required|email',
//                'application_text_en' => 'required|text',
//                'application_text_nl' => 'required|text'
            ];
    }

    public function commit(){
        $accountant = Setting::where('name', 'accountant')->first();
        $accountant->value = $this->input('accountant');
        $accountant->save();
        $applicationTextEn = Setting::where('name', 'application_text_en')->first();
        $applicationTextEn->value =  $this->input('application_text_en');
        $applicationTextEn->save();
        $applicationTextNl = Setting::where('name', 'application_text_nl')->first();
        $applicationTextNl->value = $this->input('application_text_nl');
        $applicationTextNl->save();
        $registration_status = Setting::where('name', 'registration_status')->first();
        $registration_status->value = $this->has('registration_status');
        $registration_status->save();
    }
}
