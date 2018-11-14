@extends('layouts.dashboard')

@section('content')
    <field-list-page :fields="{{$fields}}" inline-template form="{{\App\Models\Kitchen::class}}">
        <div class="box">
            <div>
                <button @click="$bus.$emit('open-create-modal')"
                        class="button is-success">@lang('global.create')</button>
            </div>
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th>@lang('global.en') @lang('global.name')</th>
                    <th>@lang('global.nl') @lang('global.name')</th>
                    <th>@lang('admin\fields.type')</th>
                    <th>@lang('global.delete')</th>
                    <th>@lang('global.edit')</th>
                </tr>
                </thead>
                <tbody is="draggable-field-list"  :given-fields="{{$fields}}" delete-btn="@lang('global.delete')" edit-btn="@lang('global.edit')">
                </tbody>
            </table>
            <div v-if="order !== []">
                <form method="POST" id="orderForm" action="{{action('Admin\FieldController@saveOrder')}}">
                    <button  type="submit" class="button is-info">Save Order</button>
                    @csrf
                    <input v-for="id in order" name="order[]" :value="id" hidden>
                </form>
            </div>
            <modal-form name="fieldForm">
                <field-form :field-form="form" :edit-field="object">
                    <template slot="csrf">
                        @csrf
                    </template>
                    <template slot="method">
                        @method('PATCH')
                    </template>
                </field-form>
            </modal-form>
        </div>
    </field-list-page>
@endsection
