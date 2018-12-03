<select-chooser :init-selected="{{ Request::input('application', 0)}}">
    @foreach($pastApplications->sortByDesc('year') as $application)
        <select-view label="{{$application->year}}">
            @component('kitchen.application.showPastApplication',compact('application'))
            @endcomponent
        </select-view>
    @endforeach
</select-chooser>

