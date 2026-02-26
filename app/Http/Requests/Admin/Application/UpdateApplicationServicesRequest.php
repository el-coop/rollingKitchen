<?php

namespace App\Http\Requests\Admin\Application;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationServicesRequest extends FormRequest {
	/**
	 * @var \Illuminate\Routing\Route|object|string
	 */
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
			'services' => 'array',
		];
	}

	public function commit() {
		$services = collect($this->input('services'));

		$services = $services->mapWithKeys(function ($quantity, $service) {
            $service = Service::find($service);
            if ($service->conditions !== null){
                return [$service->id => [
                    'quantity' => 1,
                    'equivalent_price' => $quantity, // The selected price (base or condition)
                ]];
            } else {
                return [$service->id => [
                    'quantity' => $quantity,
                ]];
            }
		})->filter(function ($item) {
			return $item['quantity'] > 0;
		});

		$this->application->services()->sync($services);

	}
}
