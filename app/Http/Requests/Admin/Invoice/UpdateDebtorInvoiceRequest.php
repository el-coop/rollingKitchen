<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendDebtorInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtorInvoiceRequest extends FormRequest {
    private $invoice;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $this->invoice = $this->route('invoice');
        return $this->user()->can('update', $this->invoice);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = collect([
            'items' => 'required|array|min:1',
            'items.*.*' => 'required',
        ]);
        if (!$this->input('file_download', false) && $this->has('send')) {
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
        $debtor = $this->invoice->owner;

        $validator->after(function ($validator) use ($debtor) {
            if (!isset($debtor->data[5]) || !isset($debtor->data[2]) || !isset($debtor->data[3]) || !isset($debtor->data[4])) {
                $validator->errors()->add('help', __('admin/invoices.billingDetailsMissing'));
            }
        });
    }


    public function commit() {
        $this->invoice = $this->route('invoice');
        if ($this->has('send') && $this->invoice->number == 0) {
            $number = Invoice::getNumber();
            $prefix = $this->invoice->prefix;
            if (strlen($number) == 1) {
                $this->invoice->number_datatable = "$prefix-00$number";
            } elseif (strlen($number) == 2) {
                $this->invoice->number_datatable = "$prefix-0$number";
            } else {
                $this->invoice->number_datatable = "$prefix-$number";
            }
            $this->invoice->number = $number;
        }
        $debtor = $this->invoice->owner;
        $number = $this->invoice->formattedNumber;

        if ($this->input('file_download', false)) {
            $invoiceService = new InvoiceService($debtor);
            $invoice = $invoiceService->generate($number, $this->input('items'),['name' => $this->extra_name, 'amount' => $this->extra_amount], null, $this->invoice->created_at);
            return $invoice->download($number);
        }
        $this->invoice->items()->delete();
        $this->invoice->tax = 0;
        $total = 0;
        foreach ($this->input('items') as $item) {
            $invoiceItem = new InvoiceItem;
            $invoiceItem->quantity = $item['quantity'];
            $invoiceItem->name = $item['item'];
            $invoiceItem->unit_price = $item['unitPrice'];
            $invoiceItem->tax = $item['tax'];
            $this->invoice->items()->save($invoiceItem);

            $total += $item['quantity'] * $item['unitPrice'] * (1 + $item['tax'] / 100);
        }

        $this->invoice->amount = $total;
        $this->invoice->extra_amount = $this->extra_amount;
        $this->invoice->extra_name = $this->extra_name;
        $this->invoice->note = $this->note;
        $this->invoice->save();
        if ($this->has('send') || $this->invoice->number != 0) {
            SendDebtorInvoice::dispatch($this->invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), collect([
                $this->input('bcc', false),
                $this->filled('accountant') ? app('settings')->get('accountant_email') : false
            ])->filter());
        }

        return $this->invoice->load('payments');
    }
}
