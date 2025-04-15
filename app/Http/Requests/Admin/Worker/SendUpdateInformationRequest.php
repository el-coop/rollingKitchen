<?php

namespace App\Http\Requests\Admin\Worker;

use App\Notifications\Worker\UpdateInformationNotification;
use Illuminate\Foundation\Http\FormRequest;

class SendUpdateInformationRequest extends FormRequest {
    protected $worker;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $this->worker = $this->route('worker');
        return $this->user()->can('update', $this->worker);    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            //
        ];
    }

    public function commit() {
        return $this->worker->user->notify(new UpdateInformationNotification());
    }
}
