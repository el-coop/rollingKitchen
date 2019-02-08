<export-worked-hours url="{{action('Admin\WorkedHoursExportColumnController@create')}}"
                     :column-options="{{$workedHoursOptions}}"
                     :fields="{{collect($workedHours)}}"></export-worked-hours>