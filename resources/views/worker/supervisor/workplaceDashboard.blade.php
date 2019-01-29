<select-chooser>
    <select-view label="@lang('admin/workers.workplace')">
        @component('worker.supervisor.workplace', compact('workplace'))
        @endcomponent
    </select-view>
    <select-view label="@lang('admin/workers.workers')">
        @component('worker.supervisor.workplaceWorkersTable', ['workersTable' => $workplace->workersForSupervisor, 'workplace' => $workplace])
        @endcomponent
    </select-view>
</select-chooser>