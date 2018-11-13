<tr>
    <td>{{$field->name}}</td>
    <td>{{$field->dutch_name}}</td>
    <td>{{$field->type}}</td>
    <td>
        @component('components.form.deleteForm', ['url' => action('Admin\FieldController@delete', $field)])
        @endcomponent
    </td>
    <td>
        <button @click="setObject({{$field}}); $modal.show('fieldForm')" class="button is-dark">@lang('global.edit')</button>
    </td>
</tr>
