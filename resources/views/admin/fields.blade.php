@extends('layouts.dashboard')

@section('title',__("admin/fields.{$type}"))

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
            <dynamic-table :responsive="true" :init-fields="{{ $fields }}" :columns="[{
					name: 'name_nl',
					responsiveHidden: {{ App::getLocale() =='en' ? 'true' : 'false' }},
					label: '@lang('admin/fields.name_nl')'
				},{
					name: 'name_en',
					responsiveHidden: {{ App::getLocale() =='nl' ? 'true' : 'false' }},
					label: '@lang('admin/fields.name_en')'
				},{
					name: 'type',
					label: '@lang('admin/fields.type')',
					type: 'select',
					responsiveHidden: true,
					options: {
						text: '@lang('admin/fields.text')',
                        textarea: '@lang('admin/fields.textarea')',
                        date: '@lang('admin/fields.date')',
					},
					translate: true
				},{
                    name: 'status',
					label: '@lang('global.status')',
					type: 'select',
					responsiveHidden: true,
					options: {
					    protected: '@lang('admin/fields.protected')',
					    encrypted: '@lang('admin/fields.encrypted')',
					    required: '@lang('admin/fields.required')',
					    none: '@lang('admin/fields.none')',
					},
					callback: 'numerateOptions'
				},
				{
					name: 'placeholder_nl',
					responsiveHidden: true,
					label: '@lang('admin/fields.placeholder_nl')',
					invisible: true
				},{
					name: 'placeholder_en',
					responsiveHidden: true,

					label: '@lang('admin/fields.placeholder_en')',
					invisible: true

				},{
				    name: 'has_tooltip',
				    label: '@lang('admin/fields.has_tooltip')',
				    invisible: true,
				    type: 'checkbox',
				    responsiveHidden: true,
				    options: {
				        yes: {name: '@lang('global.yes')', checked: false}
				    },
				    value: false
				},{
					name: 'tooltip_nl',
					invisible: true,
					label: '@lang('admin/fields.tooltip_nl')',
					invisible: true,
					responsiveHidden: true
				},{
					name: 'tooltip_en',
					invisible: true,
					label: '@lang('admin/fields.tooltip_en')',
					invisible: true,
					responsiveHidden: true
				},{
				    name: 'conditional',
				    type: 'conditional',
				    invisible: true,
				    responsiveHidden: true,
				    options: {{json_encode($conditionalOptions)}},
				    label: '@lang('admin/fields.conditional')'
				},
				]
	            " action="{{ action('Admin\FieldController@create') }}" :extra-data="{
					form: '{{ str_replace('\\','\\\\',$class) }}',
				}" :sortable="true">

            </dynamic-table>
        </div>
    </div>
@endsection
