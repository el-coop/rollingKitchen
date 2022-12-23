<?php

namespace App\Http\Requests\Kitchen;

use App\Events\Kitchen\ApplicationResubmitted;
use App\Events\Kitchen\ApplicationSubmitted;
use App\Models\Application;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKitchenRequest extends FormRequest {
    private $kitchen;
    private $application;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $this->kitchen = $this->route('kitchen');

        return $this->user()->can('update', $this->kitchen);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {


        $this->application = $this->kitchen->getCurrentApplication();
        $rules = collect([
            'name' => 'required|min:2|unique:users,name,' . $this->kitchen->user->id,
            'email' => 'required|email|unique:users,email,' . $this->kitchen->user->id,
            'language' => 'required|in:en,nl',
            'kitchen' => 'required|array',
        ]);

        if ($this->user()->can('update', $this->application) && $this->input('review')) {
            $services = Service::where('mandatory', 1)->get()->pluck('id');
            $mandatoryServices = "|required_array_keys:";
            foreach ($services as $service){
                $mandatoryServices .= "$service,";
            }
            $rules = $rules->merge([
                'kitchen.1' => 'required|min:2',
                'kitchen.2' => 'required|min:2',
                'kitchen.3' => 'required|min:2',
                'kitchen.4' => 'required|min:2',
                'kitchen.5' => 'required|min:2',
                'application' => 'required|array',
                'services' => "array" . ($services->count() ? $mandatoryServices : ''),
                'socket' => 'required|numeric',
                'length' => 'required|numeric|min:1',
                'width' => 'required|numeric|min:1',
                'terrace_length' => 'numeric|nullable|min:0',
                'terrace_width' => 'numeric|nullable|min:0',
            ]);
            if (!$this->kitchen->photos()->count()) {
                $rules = $rules->merge([
                    'kitchen.6' => 'required_without_all:kitchen.7,kitchen.11',
                    'kitchen.7' => 'required_without_all:kitchen.6,kitchen.11',
                    'kitchen.11' => 'required_without_all:kitchen.6,kitchen.7',

                ]);
            }

            if (Pdf::where("terms_and_conditions_{$this->kitchen->user->language}", true)->exists()) {
                $rules = $rules->merge([
                    'terms' => 'required'
                ]);
            }

            $fieldRules = Field::getRequiredFields(Application::class, Kitchen::class);
            $rules = $rules->merge($fieldRules);
        }
        return $rules->toArray();
    }

    public function withValidator($validator) {
        $validator->after(function($validator) {
            if ($this->input('review') && !$this->application->hasMenu()) {
                $validator->errors()->add('menu', __('kitchen/products.menuError'));
            }
        });
    }

    public function messages() {
        return [
            'kitchen.6.required_without_all' => __('kitchen/kitchen.photoValidation'),
            'kitchen.7.required_without_all' => __('kitchen/kitchen.photoValidation'),
            'kitchen.11.required_without_all' => __('kitchen/kitchen.photoValidation'),
        ];
    }

    public function commit() {
        $this->kitchen->user->name = $this->input('name');
        $this->kitchen->user->email = $this->input('email');
        $this->kitchen->user->language = $this->input('language');
        $this->kitchen->user->save();


        $this->kitchen->data = $this->input('kitchen');
        $this->kitchen->save();

        if ($this->user()->can('update', $this->application)) {

            $this->application->data = $this->input('application');
            $this->application->length = $this->input('length');
            $this->application->width = $this->input('width');
            $this->application->terrace_length = $this->input('terrace_length');
            $this->application->terrace_width = $this->input('terrace_width');
            if ($this->input('review')) {
                if ($this->application->status == 'new') {
                    event(new ApplicationSubmitted($this->application));
                } else {
                    event(new ApplicationResubmitted($this->application));
                }
                $this->application->status = 'pending';
                $this->session()->flash('fireworks', true);
            }
            $this->application->save();

            $services = collect($this->input('services'));

            if ($this->input('socket')) {
                $services->put($this->input('socket'), 1);
            }

            $services = $services->mapWithKeys(function($quantity, $service) {
                return [$service => [
                    'quantity' => $quantity,
                ]];
            })->filter(function($item) {
                return $item['quantity'] > 0;
            });

            $this->application->services()->sync($services);

        }
    }
}
