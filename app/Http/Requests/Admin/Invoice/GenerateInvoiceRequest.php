<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendApplicationInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest {
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
            'items.*.*' => 'required',
            'tax' => 'required|in:0,21'
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
        $this->application = $this->route('application');
        $kitchen = $this->application->kitchen;

        $validator->after(function($validator) use ($kitchen) {
            if (!isset($kitchen->data[5]) || !isset($kitchen->data[2]) || !isset($kitchen->data[3]) || !isset($kitchen->data[4])) {
                $validator->errors()->add('help', __('admin/invoices.billingDetailsMissing'));
            }
        });
    }


    public function commit() {
        $toSend = $this->has('send');
        if ($toSend){
            $number = Invoice::getNumber();
        } else {
            $number = 0;
        }
        $prefix = app('settings')->get('registration_year');

        if ($this->input('file_download', false)) {
            $invoiceService = new InvoiceService($this->application, $this->has('2575split'));
            $invoice = $invoiceService->generate("{$prefix}-{$number}", $this->input('items'),['name' => $this->extra_name, 'amount' => $this->extra_amount], $this->input('tax'));
            return $invoice->download("{$prefix}-{$number}");
        }
        $invoice = new Invoice;
        $invoice->prefix = $prefix;
        $invoice->number = $number;
        $invoice->tax = $this->input('tax');
        if ($toSend){
            if (strlen($number) == 1){
                $invoice->number_datatable = "$prefix-00$number";
            } elseif (strlen($number) == 2){
                $invoice->number_datatable = "$prefix-0$number";
            } else {
                $invoice->number_datatable = "$prefix-$number";
            }
        }
        $this->application->invoices()->save($invoice);
        $total = 0;
        foreach ($this->input('items') as $item) {
            $invoiceItem = new InvoiceItem;
            $invoiceItem->quantity = $item['quantity'];
            $invoiceItem->name = $item['item'];
            $invoiceItem->unit_price = $item['unitPrice'];
            if ($service = Service::where("name_en", $item['item'])->orWhere("name_nl", $item['item'])->first()) {
                $invoiceItem->service_id = $service->id;
            }

            $invoice->items()->save($invoiceItem);

            if ($invoiceItem->service_id) {
                $this->application->registerNewServices($service);
            }
            $total += $item['quantity'] * $item['unitPrice'];
        }
        $invoice->extra_name = $this->extra_name;
        $invoice->extra_amount = $this->extra_amount;
        $invoice->note = $this->note;
        $invoice->amount = $total;
        $invoice->save();

        if (!$this->application->number) {
            $this->application->setNumber();
        }
        if ($toSend){
            SendApplicationInvoice::dispatch($invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), $this->input('attachments', []), collect([
                $this->input('bcc', false),
                $this->filled('accountant') ? app('settings')->get('accountant_email') : false
            ])->filter(), $this->has('2575split'));
        }
        return $invoice->load('payments');
    }
}
