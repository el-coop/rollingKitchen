@extends('layouts.dashboard')

@section('content')
    <field-list-page inline-template form="{{\App\Models\Kitchen::class}}">
        <div class="box">
            <div>
                <button @click="$modal.show('fieldForm'); setObject(null)"
                        class="button is-success">@lang('global.create')</button>
            </div>
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Dutch Name</th>
                    <th>Type</th>
                    <th>Delete</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fields as $field)
                    @component('components.fieldListItem', ['field' => $field])
                    @endcomponent
                @endforeach
                </tbody>
            </table>
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
