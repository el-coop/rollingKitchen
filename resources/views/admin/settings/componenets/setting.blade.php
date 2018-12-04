<div class="field">
	<label class="label">@lang('admin/settings.' . $name)</label>
	<div class="control">
		{{$slot}}
	</div>
	@if($errors->has($name))
		<p class="help is-danger">{{ $errors->first($name) }}</p>
	@endif
</div>
