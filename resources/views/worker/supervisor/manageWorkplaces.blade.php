<select-chooser>
    @foreach(Auth::user()->user->workplaces as $workplace)
                <select-view label="{{$workplace->name}}">
            @component('worker.supervisor.workplaceDashboard', compact('workplace'))
            @endcomponent
        </select-view>
    @endforeach
</select-chooser>