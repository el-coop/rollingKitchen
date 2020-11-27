<?php

namespace App\Http\Requests\Kitchen;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class UsePastApplicationRequest extends FormRequest {
    private $application;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $this->application = $this->route('application');
        return $this->user()->can('update', $this->application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'pastApplication' => 'required|exists:applications,id'
        ];
    }

    public function commit() {
        $pastApplication = Application::find($this->input('pastApplication'));
        $this->application->data = $pastApplication->data;
        $this->application->length = $pastApplication->length;
        $this->application->width = $pastApplication->width;
        $this->application->terrace_length = $pastApplication->terrace_length;
        $this->application->terrace_width = $pastApplication->terrace_width;
        $products = $pastApplication->products;
        $this->application->products()->delete();
        $products->each(function ($product) {
            $replicate = $product->replicate();
            $replicate->application_id = $this->application->id;
            $replicate->save();
        });
        $electricDevices = $pastApplication->electricDevices;
        $this->application->electricDevices()->delete();
        $electricDevices->each(function ($electricDevice) {
            $replicate = $electricDevice->replicate();
            $replicate->application_id = $this->application->id;
            $replicate->save();
        });
        $services = $pastApplication->services;
        $services = collect($services->mapWithKeys(function ($service) {
            return [$service->id => ['quantity' => "{$service->pivot->quantity}"]];
        }));
        $this->application->services()->detach();
        $this->application->services()->sync($services);
        $this->application->save();
        return $this->application;
    }
}
