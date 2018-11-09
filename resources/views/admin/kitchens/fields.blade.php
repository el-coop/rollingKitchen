@extends('layouts.dashboard')

@section('content')
    <div class="box">
        <table class="table is-fullwidth">
            <thead>
                <tr>
                    <th>Name</th>
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
        <edit-modal name="editField">
            <div slot-scope="slotProps">
                @{{slotProps.object}}
            </div>
        </edit-modal>
    </div>
@endsection