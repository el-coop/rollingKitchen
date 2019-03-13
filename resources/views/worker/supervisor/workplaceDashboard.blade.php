<select-chooser>
	<select-view label="@lang('admin/workers.workers')">
		@component('worker.supervisor.workplaceWorkersTable', ['workersTable' => $workplace->workersForSupervisor, 'workplace' => $workplace, 'formattersData' => $formattersData])
		@endcomponent
	</select-view>
	<select-view label="@lang('worker/worker.shifts')">
		@component('worker.supervisor.workplaceShiftsTable', ['shiftsTable' => $workplace->shiftsForSupervisor, 'workplace' => $workplace])
		@endcomponent
	</select-view>
</select-chooser>
