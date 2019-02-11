<!doctype html>
<html lang="{{ App::getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>@yield('title') | {{ config('app.name') }}</title>

	<link href="{{ env('APP_URL') . mix('/css/app.css') }}" rel="stylesheet">


</head>
<body>
<div class="section">
	<div class="table-container">
		<table class="table is-fullwidth">
			<thead>
			<tr>
				<td>@lang('admin/shifts.date')</td>
				<td>@lang('worker/worker.workplace')</td>
				<td>@lang('vue.workFunction')</td>
				<td>@lang('admin/shifts.startTime')</td>
				<td>@lang('admin/shifts.endTime')</td>
			</tr>
			</thead>
			<tbody>
			@foreach($shifts as $shift)

				<tr>
					<td>{{$shift->date}}</td>
					<td>{{$shift->workplace->name}}</td>
					<td>{{$shift->workplace->workfunctions->firstWhere('id',$shift->pivot->work_function_id)->name}}</td>
					<td>{{ date('H:i',strtotime($shift->pivot->start_time)) }}</td>
					<td>{{ date('H:i',strtotime($shift->pivot->end_time)) }}</td>
				</tr>
			@endforeach
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>@lang('admin/workers.totalHours')</td>
				<td>{{ floor($totalHoursWorked->total('hours')) . ":{$totalHoursWorked->minutes}" }}</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
</body>
</html>
