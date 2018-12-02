<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>@lang('global.title')</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
		  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<style>
		h1, h2, h3, h4, p, span, div {
			font-family: DejaVu Sans;
		}
	</style>
</head>
<body>
<div style="clear:both; position:relative;">
	<div style="position:absolute; left:0pt; width:250pt;">
		<img class="img-rounded" height="{{ $invoice->logo_height }}" src="{{ $invoice->logo }}">
	</div>
	<div style="margin-left:300pt;">
		<b>@lang('admin/invoices.date'): </b> {{ $invoice->date->formatLocalized('%A %d %B %Y') }}<br/>
		@if ($invoice->number)
			<b>@lang('admin/invoices.invoice') #: </b> {{ $invoice->number }}
		@endif
		<br/>
	</div>
</div>
<br/>
<h2>@lang('global.title') {{ $invoice->number ? '#' . $invoice->number : '' }}</h2>
<div style="clear:both; position:relative;">
	<div style="position:absolute; left:0pt; width:250pt;">
		<h4>@lang('admin/invoices.businessDetails'):</h4>
		<div class="panel panel-default">
			<div class="panel-body">
				{!! $invoice->business_details->count() == 0 ? '<i>No business details</i><br />' : '' !!}
				{{ $invoice->business_details->get('name') }}<br/>
				{{ $invoice->business_details->get('phone') }}<br/>
				{{ $invoice->business_details->get('location') }}<br/>
				{{ $invoice->business_details->get('zip') }} {{ $invoice->business_details->get('city') }}
				{{ $invoice->business_details->get('country') }}<br/>
			</div>
		</div>
	</div>
	<div style="margin-left: 300pt;">
		<h4>@lang('admin/invoices.customerDetails'):</h4>
		<div class="panel panel-default">
			<div class="panel-body">
				{!! $invoice->customer_details->count() == 0 ? '<i>No customer details</i><br />' : '' !!}
				{{ $invoice->customer_details->get('name') }}<br/>
				{{ $invoice->customer_details->get('phone') }}<br/>
				{{ $invoice->customer_details->get('location') }}<br/>
				{{ $invoice->customer_details->get('zip') }} {{ $invoice->customer_details->get('city') }}
				{{ $invoice->customer_details->get('country') }}<br/>
			</div>
		</div>
	</div>
</div>
<h4>@lang('admin/invoices.items'):</h4>
<table class="table table-bordered">
	<thead>
	<tr>
		<th>#</th>
		<th>@lang('vue.item')</th>
		<th>@lang('vue.unitPrice')</th>
		<th>@lang('vue.quantity')</th>
		<th>@lang('vue.total')</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($invoice->items as $item)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $item->get('name') }}</td>
			<td>{{ $invoice->formatCurrency()->symbol }} {{ $item->get('price') }} </td>
			<td>{{ $item->get('ammount') }}</td>
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
					{{ $invoice->notes }}
				</div>
			</div>
		</div>
	@endif
	<div style="margin-left: 300pt;">
		<h4>@lang('vue.total'):</h4>
		<table class="table table-bordered">
			<tbody>
			<tr>
				<td><b>@lang('admin/invoices.subtotal')</b></td>
				<td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->subTotalPriceFormatted() }}</td>
			</tr>
			<tr>
				<td>
					<b>
						@lang('vue.vat') {{ $invoice->tax_type == 'percentage' ? '(' . $invoice->tax . '%)' : '' }}
					</b>
				</td>
				<td>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->taxPriceFormatted() }}</td>
			</tr>
			<tr>
				<td><b>@lang('vue.total')</b></td>
				<td><b>{{ $invoice->formatCurrency()->symbol }} {{ $invoice->totalPriceFormatted() }}</b></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
@if ($invoice->footnote)
	<br/><br/>
	<div class="well">
		{{ $invoice->footnote }}
	</div>
@endif
</body>
</html>
