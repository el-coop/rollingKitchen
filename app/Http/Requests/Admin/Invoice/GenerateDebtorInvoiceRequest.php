<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendDebtorInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class GenerateDebtorInvoiceRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = collect([
            'items' => 'required|array|min:1',
            'items.*.*' => 'required'
        ]);
        if (!$this->input('file_download', false)) {
            $rules = $rules->merge([
                'recipient' => 'required|email',
                'bcc' => 'nullable|email',
                'message' => 'required|string',
                'subject' => 'required|string',
            ]);
        }
        return $rules->toArray();
    }

    public function withValidator($validator) {
        $this->debtor = $this->route('debtor');

        $validator->after(function ($validator) {
            if (!isset($this->debtor->data[5]) || !isset($this->debtor->data[2]) || !isset($this->debtor->data[3]) || !isset($this->debtor->data[4])) {
                $validator->errors()->add('help', __('admin/invoices.billingDetailsMissing'));
            }
        });
    }

    public function commit() {
        $toSend = $this->has('send');
        if ($toSend) {
            $number = Invoice::getNumber();
        } else {
            $number = 0;
        }
        $prefix = app('settings')->get('registration_year');

        if ($this->input('file_download', false)) {
            $invoiceService = new InvoiceService($this->debtor);
            $invoice = $invoiceService->generate("{$prefix}-{$number}", $this->input('items'));
            return $invoice->download("{$prefix}-{$number}");
        }
        $invoice = new Invoice;
        $invoice->prefix = $prefix;
        $invoice->number = $number;
        $invoice->tax = 0;
        if ($toSend){
            if (strlen($number) == 1){
                $invoice->number_datatable = "$prefix-00$number";
            } elseif (strlen($number) == 2){
                $invoice->number_datatable = "$prefix-0$number";
            } else {
                $invoice->number_datatable = "$prefix-$number";
            }
        }
        $this->debtor->invoices()->save($invoice);
        $total = 0;
        foreach ($this->input('items') as $item) {
            $invoiceItem = new InvoiceItem;
            $invoiceItem->quantity = $item['quantity'];
            $invoiceItem->name = $item['item'];
            $invoiceItem->unit_price = $item['unitPrice'];
            $invoiceItem->tax = $item['tax'];
            $invoice->items()->save($invoiceItem);

            $total += $item['quantity'] * $item['unitPrice'] * (1 + $item['tax'] / 100);
        }

        $invoice->extra_name = $this->extra_name;
        $invoice->extra_amount = $this->extra_amount;
        $invoice->note = $this->note;
        $invoice->amount = $total;
        $invoice->save();
        if ($toSend){
            SendDebtorInvoice::dispatch($invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), collect([
                $this->input('bcc', false),
                $this->filled('accountant') ? app('settings')->get('accountant_email') : false
            ])->filter());
        }
        return $invoice->load('payments');
    }
}
