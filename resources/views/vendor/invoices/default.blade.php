<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@lang('global.title')</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        h1, h2, h3, h4, p, span, div, th, td {
            font-family: DejaVu Sans;
            font-size: 10px;

        }
    </style>
</head>
<body>
<div style="clear:both; position:relative;">
    <div style="position:absolute; left:0pt; width:250pt;">
        <img class="img-rounded" height="{{ $invoice->logo_height }}" src="{{ $invoice->logo }}">
    </div>
    <div style="margin-left:300pt;">
        <b>@lang('admin/invoices.date',[],$invoice->language)
            : </b> {{ ucfirst($invoice->date->isoFormat('dddd DD MMMM Y')) }}
        <br/>
        @if ($invoice->number)
            <b>@lang('admin/invoices.invoice',[],$invoice->language) #: </b> {{ $invoice->number }}
        @endif
        <br/>
    </div>
</div>
<br/>
<h2>@lang('global.title',[],$invoice->language) {{ $invoice->number ? '#' . $invoice->number : '' }}</h2>
<div style="clear:both; position:relative;">
    <div style="position:absolute; left:0pt; width:250pt;">
        <h4>@lang('admin/invoices.businessDetails',[],$invoice->language):</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                {!! $invoice->business_details->first() !!}
            </div>
        </div>
    </div>
    <div style="margin-left: 300pt;">
        <h4>@lang('admin/invoices.customerDetails',[],$invoice->language):</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                {!! $invoice->customer_details->count() == 0 ? '<i>No customer details</i><br />' : '' !!}
                {{ $invoice->customer_details->get('name') }}<br/>
                {{ $invoice->customer_details->get('contactPerson') }}<br/>
                {{ $invoice->customer_details->get('phone') }}<br/>
                {{ $invoice->customer_details->get('location') }}<br/>
                {{ $invoice->customer_details->get('zip') }} {{ $invoice->customer_details->get('city') }}
                {{ $invoice->customer_details->get('country') }}<br/>
            </div>
        </div>
    </div>
</div>
<h4>@lang('admin/invoices.items',[],$invoice->language):</h4>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>@lang('vue.item',[],$invoice->language)</th>
        <th>@lang('vue.unitPrice',[],$invoice->language)</th>
        <th>@lang('vue.quantity',[],$invoice->language)</th>
        @if($invoice->tax_type == 'individual')
            <th>@lang('vue.vat',[],$invoice->language)</th>
        @endif
        <th>@lang('vue.total',[],$invoice->language)</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoice->items as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->get('name') }}</td>
            <td>{{ $invoice->formatCurrency()->symbol }} {{ $item->get('formattedPrice') }} </td>
            <td>{{ $item->get('amount') }}</td>
            @if($invoice->tax_type == 'individual')
                <td>{{ $item->get('tax') }}%</td>
            @endif
            <td>{{ $invoice->formatCurrency()->symbol }} {{ $item->get('totalPrice') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="clear:both; position:relative;">
    @if($invoice->notes)
        <div style="position:absolute; left:0pt; width:250pt;">
            <h4>Notes:</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    {!!  $invoice->notes  !!}
                </div>
            </div>
        </div>
    @endif
    <div style="margin-left: 300pt;">
        <h4>@lang('vue.total',[],$invoice->language):</h4>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>
                    <b>@lang('admin/invoices.subtotal',[],$invoice->language)</b>
                </td>
                <td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->subTotalPriceFormatted() }}</td>
            </tr>
            <tr>
                <td>
                    <b>
                        @lang('vue.vat',[],$invoice->language) {{ $invoice->tax_type == 'percentage' ? '(' . $invoice->tax . '%)' : '' }}
                    </b>
                </td>
                <td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->taxPriceFormatted() }}</td>
            </tr>
            <tr>
                <td>
                    <b>@lang('vue.total',[],$invoice->language)</b>
                </td>
                <td><b>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->totalPriceFormatted() }}</b></td>
            </tr>
            </tbody>
        </table>
        @if($invoice->split)
            <h4>@lang('admin/invoices.terms',[],$invoice->language):</h4>
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td>
                        <b>
                            @lang('admin/invoices.payNow',[],$invoice->language)
                        </b>
                    </td>
                    <td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->totalPriceFormatted(.25, true) }}</td>
                </tr>
                <tr>
                    <td>
                        <b>
                            @lang('admin/invoices.payLater',[],$invoice->language)
                        </b>
                    </td>
                    <td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->totalPriceFormatted(.75) }}
                    </td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
@if ($invoice->footnote)
    <br/><br/>
    <div class="well text-center">
        {!!  $invoice->footnote  !!}
    </div>
@endif
</body>
</html>
