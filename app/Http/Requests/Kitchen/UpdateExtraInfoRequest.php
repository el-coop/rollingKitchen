<?php

namespace App\Http\Requests\Kitchen;

use App\Events\Kitchen\ExtraInfoSubmitted;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExtraInfoRequest extends FormRequest {

    private $application;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        $this->application = $this->route('application');
        return $this->user()->can('updateExtraInfo', $this->application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $rules = $this->yesNoFields()->mapWithKeys(function ($field) {
            return [$field => 'nullable|boolean'];
        })->merge([
            'outside_serving_depth' => 'nullable|numeric|min:0',
            'path_distance' => 'nullable|numeric|min:0',
            'religious_notes' => 'array',
            'religious_notes.*' => 'nullable|string',
            'vegan_products' => 'array',
            'other_details' => 'nullable|string',
        ]);

        if ($this->input('review')) {
            $rules = $rules->merge($this->yesNoFields()->mapWithKeys(function ($field) {
                return [$field => 'required|boolean'];
            }))->merge([
                'outside_serving_depth' => 'required_if:outside_serving,1|nullable|numeric|min:0',
                'path_distance' => 'required|numeric|min:0',
                'cooks_in_backstage' => 'required_if:needs_backstage,1|nullable|boolean',
            ]);
        }
        return $rules->toArray();
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            if (!$this->input('review')) {
                return;
            }
            $dishIds = $this->dishes()->pluck('id');
            if ($this->input('uses_electric_devices') && !$this->application->electricDevices()->exists()) {
                $validator->errors()->add('uses_electric_devices', __('kitchen/extraInfo.devicesRequired'));
            }
            if ($this->input('religious_dishes') && collect($this->input('religious_notes'))->only($dishIds)->filter()->isEmpty()) {
                $validator->errors()->add('religious_dishes', __('kitchen/extraInfo.religiousNotesRequired'));
            }
            if ($this->input('vegan_dishes') && $dishIds->intersect($this->input('vegan_products'))->isEmpty()) {
                $validator->errors()->add('vegan_dishes', __('kitchen/extraInfo.veganProductsRequired'));
            }
        });
    }

    public function messages() {
        return $this->yesNoFields()->mapWithKeys(function ($field) {
            return ["{$field}.required" => __('kitchen/extraInfo.required')];
        })->merge([
            'cooks_in_backstage.required_if' => __('kitchen/extraInfo.required'),
            'outside_serving_depth.required_if' => __('kitchen/extraInfo.required'),
            'path_distance.required' => __('kitchen/extraInfo.required'),
        ])->toArray();
    }

    private function yesNoFields() {
        return collect([
            'outside_serving',
            'own_terrace_furniture',
            'needs_backstage',
            'cooks_in_backstage',
            'open_flame',
            'uses_gas',
            'uses_electric_devices',
            'deep_fryer',
            'religious_dishes',
            'vegan_dishes',
        ]);
    }

    private function dishes() {
        return $this->application->products()->where('category', '!=', 'other')->get();
    }

    public function commit() {
        $this->yesNoFields()->each(function ($field) {
            $this->application->$field = $this->input($field);
        });
        $this->application->outside_serving_depth = $this->application->outside_serving ? $this->input('outside_serving_depth') : null;
        $this->application->path_distance = $this->input('path_distance');
        if (!$this->application->needs_backstage) {
            $this->application->cooks_in_backstage = null;
        }
        $this->application->other_details = $this->input('other_details');
        if ($this->input('review')) {
            $this->application->extra_info_submitted_at = now();
        }
        $this->application->save();

        if ($this->application->uses_electric_devices === false) {
            $this->application->electricDevices->each(function ($device) {
                $device->delete();
            });
        }

        $religiousNotes = collect($this->input('religious_notes'));
        $veganProducts = collect($this->input('vegan_products'));
        $this->dishes()->each(function ($product) use ($religiousNotes, $veganProducts) {
            $product->religious_note = $this->application->religious_dishes ? $religiousNotes->get($product->id) : null;
            $product->vegan = $this->application->vegan_dishes && $veganProducts->contains($product->id);
            $product->save();
        });

        if ($this->input('review')) {
            event(new ExtraInfoSubmitted($this->application));
        }
    }
}
