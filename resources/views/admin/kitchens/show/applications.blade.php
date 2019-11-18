<select-chooser :init-selected="{{ Request::input('application', 0)}}">
	@foreach($kitchen->applications->sortByDesc('year') as $application)
		<select-view label="{{$application->year}}">
			@component('admin.kitchens.show.application',compact('application', 'services'))
			@endcomponent
		</select-view>
	@endforeach
</select-chooser>
