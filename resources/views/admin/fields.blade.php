@extends('layouts.dashboard')

@section('content')
	<div class="card">
		<div class="card-content">
			<h4 class="title is-4">
				@lang("admin/fields.{$type}")
			</h4>
			<div class="subtitle">
				<a href="{{ $indexLink }}">@lang('admin/fields.back')</a>
			</div>
			<hr>
		</div>
		<div class="card-content">
			<dynamic-table :init-fields="{{ $fields }}" :columns="[{
					name: 'name_nl',
					label: '@lang('admin/fields.name_nl')'
				},{
					name: 'name_en',
					label: '@lang('admin/fields.name_en')'
				},{
					name: 'type',
					label: '@lang('admin/fields.type')',
					type: 'select',
					options: {
						text: '@lang('admin/fields.text')',
						textarea: '@lang('admin/fields.textarea')',
					},
					translate: true
				}]" action="{{ action('Admin\FieldController@create') }}" :extra-data="{
					form: '{{ str_replace('\\','\\\\',$class) }}',
				}" :sortable="true">

			</dynamic-table>
		</div>
	</div>
@endsection
