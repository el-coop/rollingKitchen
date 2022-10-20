@extends('layouts.dashboard')

@section('title', __('admin/invoices.invoices'))

@section('content')
	<div>
		@component('components.datatable')

			@slot('filters',$filters)
			@slot('editWidth',1000)
			<template #default="{object, onUpdate}">
				<template v-if="object">
					<div class="title is-7 has-text-centered">
						<component
								:is="object.owner_type.substring(11).toLowerCase() === 'deletedinvoiceowner' ? 'div' : 'a'"
								:href="`/admin/${object.owner_type.substring(11).toLowerCase()}s/${object.owner_id}`">
							<span class="is-size-3" v-text="object.name"></span>
							<font-awesome-icon icon="link"
											   v-if="object.owner_type.substring(11).toLowerCase() !== 'deletedinvoiceowner'"></font-awesome-icon>
						</component>
					</div>
					<dynamic-form
							:url="`{{Request::url() }}/${object.owner_type.substring(11).toLowerCase()}/${object.owner_id}/${object.id}`"
							:on-data-update="onUpdate" button-text="@lang('admin/invoices.send')"></dynamic-form>
					<div class="mt-4">
                        <invoice-payments-modal #default="{open}" :from-url="true" :on-update="onUpdate" :field="object">
			    			<button class="button is-fullwidth is-success"
		    						@click="open">
	    						@lang('admin/invoices.managePayments')
    						</button>
                        </invoice-payments-modal>
					</div>
				</template>
			</template>
		@endcomponent
	</div>

@endsection
