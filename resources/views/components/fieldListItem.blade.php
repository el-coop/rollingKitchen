<tr>
    <td>{{$field->name}}</td>
    <td>{{$field->type}}</td>
    <td>
        @component('components.form.deleteForm', ['url' => action('Admin\FieldController@delete', $field)])
        @endcomponent
    </td>
    <td>
        <button @click="$bus.$emit('open-edit-modal', {{$field}}); $modal.show('editField')" class="button is-dark">@lang('global.edit')</button>

    </td>
</tr>