<div class="table-container">
    <table class="table is-fullwidth">
        <thead>
        <tr>
            <th>
                Date
            </th>
            <th>
                Workplace
            </th>
            <th>
                Work Function
            </th>

            <th>
                Starting Time
            </th>
            <th>
                End Time
            </th>
        </tr>
        </thead>
        <tbody>

        @foreach($shifts as $shift )

            <tr>
                <td>
                    {{$shift->date->format('d/m/Y')}}
                </td>

                <td>
                    {{$shift->workplace->name}}
                </td>
                <td>
                    {{$shift->workplace->workfunctions()->find($shift->pivot->work_function_id)->name}}
                </td>

                <td>
                    {{date('H:i',strtotime($shift->pivot->start_time))}}
                </td>
                <td>
                    {{date('H:i',strtotime($shift->pivot->end_time))}}
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>