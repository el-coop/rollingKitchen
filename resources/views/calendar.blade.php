@extends('layouts.site')

@section('content')
	<ajax-form>
		<div class="buttons">
			<button class="button is-dark">SUBMIT</button>
		</div>
		<calendar start-date="{{ \Carbon\Carbon::now() }}" :start-hour="17" options-title="Bands" :init-data="{'25/02/2019 17:00':[{
		band: 1,
		stage: 2,
		payment: 200
	},{
		band: 3,
		stage: 1,
		payment: 500
	}],'01/03/2019 23:30':[{
		band: 2,
		stage: 1,
		payment: 200
	},{
		band: 4,
		stage: 2,
		payment: 500
	}]}"
				  :options="[{
		id: 1,
		name: 'Band 1'
	},{
		id: 2,
	name: 'Band 2'
	},{
		id: 3,
	name: 'Band 3'
	},{
		id: 4,
	name: 'Band 4'
	}]">
			<template #entry="{rawData,processedData, edit, init, dateTime}">
				<calendar-schedule-display v-if="processedData" :data="processedData" :edit="edit" :init="init" :bands="{
			1: 'Band 1',
			2: 'Band 2',
			3: 'Band 3',
			4: 'Band 4',
		}" :stages="{
			1: 'Stage 1',
			2: 'Stage 2',
		}" :date-time="dateTime"></calendar-schedule-display>
			</template>
			<template #modal="{input, output}">
				<calendar-modal :input="input" :output="output" :stages="{1: 'Stage 1',2: 'Stage 2'}" :bands="{
			1: 'Band 1',
			2: 'Band 2',
			3: 'Band 3',
			4: 'Band 4',
		}"></calendar-modal>
			</template>
		</calendar>
	</ajax-form>
@endsection
