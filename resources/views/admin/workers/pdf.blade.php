<!doctype html>
<html lang="{{ App::getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ config('app.name') }}</title>

	<link href="{{ env('APP_URL') . mix('/css/app.css') }}" rel="stylesheet">

	<style>
		* {
			font-size: 12px;
		}
	</style>
</head>
<body>
<div class="section">
	<div class="container">
		<div class="is-pulled-left">
			<figure class="image is-32x32" style="margin-left: 35px">
				<img src="{{ asset('/images/logo.png')}}">
			</figure>
			<div class="has-text-4">{{ config('app.name') }}</div>
		</div>
		<div class="is-pulled-right">
			<b>@lang('admin/invoices.date')
				: </b> {{ ucfirst(\Carbon\Carbon::now()->isoFormat('dddd DD MMMM Y')) }}
		</div>
	</div>
</div>
<div class="is-clearfix"></div>
<div class="section" style="padding-bottom: 10px; margin-bottom: 10px">
	@foreach($workerData as $key => $value)
		<div>
			<span class="has-text-weight-bold">{{ $key }}:</span> {{ $value }}
		</div>
	@endforeach
</div>
<div class="section" style="padding-top: 10px; margin-top: 10px; margin-bottom: 50px">
	<h5 class="title is-5">@lang('worker/worker.workedHours')</h5>
	<table class="table is-fullwidth">
		<thead>
		<tr>
			<td>@lang('admin/shifts.date')</td>
			<td>@lang('worker/worker.workplace')</td>
			<td>@lang('vue.workFunction')</td>
			<td>@lang('admin/shifts.startTime')</td>
			<td>@lang('admin/shifts.endTime')</td>
			<td>@lang('admin/workers.shiftPayment')</td>
		</tr>
		</thead>
		<tbody>
		@foreach($shifts as $shift)
			<tr>
				<td>{{$shift->date}}</td>
				<td>{{$shift->workplace->name}}</td>
				<td>
					{{$shift->workplace->workFunctions->firstWhere('id',$shift->pivot->work_function_id)->name ?? __('global.deleted')}}
				</td>
				<td>{{ date('H:i',strtotime($shift->pivot->start_time)) }}</td>
				<td>{{ date('H:i',strtotime($shift->pivot->end_time)) }}</td>
				<td>{{ number_format($shift->pivot->payment,2,$decimalPoint,$thousandSeparator) }}</td>
			</tr>
		@endforeach
		<tr>
			<td></td>
			<td></td>
			<td>@lang('admin/workers.totalHours')</td>
			<td>
				{{number_format($worker->workedHours->total('hours'),2,$decimalPoint,$thousandSeparator)}}
			</td>
			<td>@lang('admin/workers.totalPayment')</td>
			<td>{{ number_format($worker->totalPayment,2,$decimalPoint,$thousandSeparator) }}</td>
		</tr>
		</tbody>
	</table>
</div>
<div class="is-clearfix"></div>
<div class="section">
	<div class="container">
		@foreach($images as $image)
			<img src="{{ $image }}">
		@endforeach
	</div>
</div>
</body>
</html>
