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
                'application_text_en' => 'required|string',
                'application_text_nl' => 'required|string'
            ];
    }

    public function commit(){
        $names = DB::table('settings')->select('name')->get();
        foreach ($names as $name){
            $setting = Setting::where('name', $name->name)->first();
            if ($name->name === 'registration_status'){
                $setting->value = $this->has($name->name);
            } else {
                $setting->value = $this->input($name->name);
            }
            $setting->save();
        }
    }
}
