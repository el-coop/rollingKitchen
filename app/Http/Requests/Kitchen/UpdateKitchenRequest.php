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
            $applicationRequired = Field::where('form', Application::class)->exists() ? 'required|' : '';
            $mandatoryServices = trim($mandatoryServices,',');
            $rules = $rules->merge([
                'kitchen.1' => 'required|min:2',
                'kitchen.2' => 'required|min:2',
                'kitchen.3' => 'required|min:2',
                'kitchen.4' => 'required|min:2',
                'kitchen.5' => 'required|min:2',
                'application' => "$applicationRequired" . 'array',
                'services' => "array" . ($services->count() ? $mandatoryServices : ''),
                'socket' => 'required|numeric',
                'length' => 'required|numeric|min:1',
                'width' => 'required|numeric|min:1',
                'story' => 'required|string',
                'description' => 'required|string',
                'sells_drinks' => 'required|boolean',
            ]);

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
            if ($this->input('review') && $this->filled('description') && $this->descriptionWordCount() < 30) {
                $validator->errors()->add('description', __('kitchen/dimensions.descriptionWordCount'));
            }
        });
    }

    public function messages() {
        return [
            'sells_drinks.required' => __('kitchen/products.drinksRequired'),
        ];
    }

    private function descriptionWordCount() {
        return count(preg_split('/\s+/u', trim($this->input('description')), -1, PREG_SPLIT_NO_EMPTY));
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
            $this->application->story = $this->input('story');
            $this->application->description = $this->input('description');
            $this->application->sells_drinks = $this->input('sells_drinks');
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

            if ($this->application->sells_drinks === false) {
                $this->application->products()->where('category', 'other')->get()->each(function ($product) {
                    $product->delete();
                });
            }

            $services = collect($this->input('services'));

            if ($this->input('socket')) {
                $services->put($this->input('socket'), 1);
            }

            $serviceModels = Service::whereIn('id', $services->keys())->get()->keyBy('id');
            $services = $services->mapWithKeys(function ($value, $serviceId) use ($serviceModels) {
                $service = $serviceModels[$serviceId] ?? null;
                if ($service && $service->type == 3)     {
                    return [
                        $serviceId => [
                            'quantity'          => 1,
                            'equivalent_price'  => $value,
                        ]
                    ];
                }
                return [
                    $serviceId => [
                        'quantity' => $value,
                    ]
                ];
            })->filter(function ($item) {
                return $item['quantity'] > 0;
            });
            $this->application->services()->sync($services);

        }
    }
}
