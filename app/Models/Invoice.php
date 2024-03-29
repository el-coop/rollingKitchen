<?php

namespace App\Models;

use App\Services\InvoiceService;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\App;

class Invoice extends Model {
    use HasFactory;

    protected $appends = [
        'total',
        'taxAmount',
        'formattedNumber',
        'formattedTotal',
        'totalPaid',
        'amountLeft'
    ];

    protected static function boot() {
        parent::boot();
        static::deleted(function ($invoice) {
            $invoice->items->each->delete();
        });
    }

    static function getNumber() {

        $year = app('settings')->get('registration_year');
        $number = static::where('prefix', $year)->where('number', '!=', 0)->count() + 1;
        $padding = '';
        if ($number < 100) {
            $padding .= 0;
        }
        if ($number < 10) {
            $padding .= 0;
        }
        return "{$padding}{$number}";
    }

    public function getFormattedNumberAttribute() {
        $padding = '';
        if (strlen($this->number) == 1) {
            $padding = '00';
        } else if (strlen($this->number) == 2) {
            $padding = '0';
        }

        return "{$this->prefix}-{$padding}{$this->number}";
    }

    public function getTaxAmountAttribute() {
        return $this->amount * $this->tax / 100;
    }

    public function getTotalAttribute() {
        return $this->amount  + $this->taxAmount + $this->extra_amount;

    }

    public function getFormattedTotalAttribute() {
        $decimalPoint = App::getLocale() == 'nl' ? ',' : '.';
        $thousandSeparator = App::getLocale() == 'nl' ? '.' : ',';
        return number_format($this->total, 2, $decimalPoint, $thousandSeparator);
    }

    public function getFullDataAttribute() {
        $language = $this->owner instanceof Application ? $this->owner->kitchen->user->language : $this->owner->language;
        $settings = app('settings');

        $pdfs = collect([]);
        $options = collect([]);
        $items = $this->formattedItems;
        $subject = '';
        $message = '';
        $individualTax = true;
        $taxOptions = [
            '21' => '21%',
            '9' => '9%',
            '6' => '6%',
            '0' => '0',
        ];
        if ($this->owner instanceof Application) {
            $taxOptions = [
                '21' => '21%',
                '0' => '0',
            ];
            $individualTax = false;
            $invoiceService = new InvoiceService($this->owner);
            $options = $invoiceService->getOptions($language);
            $pdfs = Pdf::allForInvoice($this->exists);
            if ($this->exists) {
                $subject = $settings->get("invoices_default_resend_subject_{$language}", '');
                $message = $settings->get("invoices_default_resend_email_{$language}", '');
            } else {
                $items = $invoiceService->getOutstandingItems($language);
                $subject = $settings->get("invoices_default_subject_{$language}", '');
                $message = $settings->get("invoices_default_email_{$language}", '');
            }
        }

        $fullData = [[
            'name' => 'note',
            'label' => __('global.note'),
            'type' => 'textarea',
            'value' => $this->note
        ],
            [
            'name' => 'recipient',
            'label' => __('admin/invoices.recipient'),
            'type' => 'text',
            'value' => $this->owner instanceof Application ? $this->owner->kitchen->user->email : $this->owner->email,
        ], [
            'name' => 'bcc',
            'label' => __('admin/invoices.bcc'),
            'type' => 'text',
            'value' => Auth::user()->email,
        ], [
            'name' => 'accountant',
            'label' => false,
            'type' => 'checkbox',
            'options' => [[
                'name' => __('admin/invoices.accountant'),
                'checked' => true
            ]],
        ], [
            'name' => 'subject',
            'label' => __('admin/invoices.subject'),
            'type' => 'text',
            'checked' => true,
            'value' => $subject,
        ], [
            'name' => 'message',
            'label' => __('admin/invoices.message'),
            'type' => 'textarea',
            'value' => $message,
        ],
//            [
//            'name' => '2575split',
//            'label' => '',
//            'type' => 'checkbox',
//            'options' => ['2575split' => ['name' =>  __('admin/invoices.2575split'), 'checked' => true]],
//        ],
            [
                'name' => 'attachments',
                'label' => __('admin/invoices.attachments'),
                'type' => 'checkbox',
                'options' => $pdfs
            ], [
                'name' => 'items',
                'label' => 'Items',
                'type' => 'invoice',
                'value' => $items,
                'individualTax' => $individualTax,
                'options' => $options,
                'taxOptions' => $taxOptions,
                'extra_name' => $this->extra_name,
                'extra_amount' => $this->extra_amount
            ], [
                'name' => 'help',
                'label' => '',
                'type' => 'help',
            ],
        ];
        if ($this->number === 0 || $this->number === null) {
            $fullData[] = [
                'name' => 'send',
                'label' => false,
                'type' => 'checkbox',
                'options' => [[
                    'name' => __('admin/invoices.send'),
                    'checked' => false
                ]]
            ];
        }
        $fullData[] = [
            'name' => 'file_download',
            'label' => __('admin/invoices.preview'),
            'type' => 'alternative-submit',
            'value' => true
        ];
        return $fullData;
    }

    public function owner() {
        return $this->morphTo();
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getFormattedItemsAttribute() {
        return $this->items->map(function ($item) {
            return [
                'quantity' => $item->quantity,
                'item' => $item->name,
                'tax' => $item->tax,
                'unitPrice' => $item->unit_price,
            ];
        });
    }

    public function services() {
        return $this->hasManyThrough(Service::class, InvoiceItem::class);
    }

    public function payments() {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getTotalPaidAttribute() {
        return $this->payments->sum('amount');
    }

    public function getAmountLeftAttribute() {
        return $this->total - $this->totalPaid;
    }
}
