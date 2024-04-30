<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class DestroyInvoiceRequest extends FormRequest {
    private $invoice;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $this->invoice = $this->route('invoice');
        return $this->user()->can('delete', $this->invoice);
    }

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
        return $this->invoice->delete();
    }
}
