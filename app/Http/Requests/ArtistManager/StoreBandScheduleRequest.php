<?php

namespace App\Http\Requests\ArtistManager;

use App\Events\Band\ShowCreated;
use App\Events\Band\ShowDeleted;
use App\Events\Band\ShowUpdated;
use App\Models\Band;
use App\Models\BandSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreBandScheduleRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('schedule', Band::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'calendar' => 'array',
			'calendar.*' => 'required|array',
			'calendar.*.*' => 'array',
			'calendar.*.*.band' => 'required|exists:bands,id',
			'calendar.*.*.payment' => 'required|min:1',
			'calendar.*.*.stage' => 'required|exists:stages,id',
			'calendar.*.*.endTime' => 'required|date_format:H:i',
		];
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			$budget = app('settings')->get('schedule_budget');
			if (is_array($this->input('calendar'))) {
				if (collect($this->input('calendar'))->sum(function ($dateTime) {
						return collect($dateTime)->sum(function ($show) {
							return $show['payment'];
						});
					}) > $budget) {
					$validator->errors()->add('payment', __('vue.budgetOverflow'));
				}
			}
		});
	}

	public function commit() {

		$existingSchedules = BandSchedule::all();
		$newSchedules = collect([]);
		foreach ($this->input('calendar', []) as $dateTime => $shows) {
			foreach ($shows as $show) {
				$endTime = explode(':', $show['endTime']);
				$schedule = new BandSchedule;
				$schedule->band_id = $show['band'];
				$schedule->date_time = Carbon::createFromFormat('d/m/Y H:i', $dateTime);
				$schedule->stage_id = $show['stage'];
				$schedule->payment = $show['payment'];
				$schedule->end_time = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->hours((int)$endTime[0])->minutes((int)$endTime[1]);
				$schedule->approved = 'pending';
				$newSchedules->push($schedule);
			}
		}

		BandSchedule::query()->delete();
		$this->deleteOldSchedules($existingSchedules, $newSchedules);
		$this->persistNewSchedules($existingSchedules, $newSchedules);
	}

	public function deleteOldSchedules($existingSchedules, $newSchedules) {
		$existingSchedules->each(function ($schedule) use ($newSchedules) {
			if (!$newSchedules->where('band_id', $schedule->band_id)->where('date_time', $schedule->date_time)->first()) {
				event(new ShowDeleted($schedule));
			}
		});
	}


	public function persistNewSchedules($existingSchedules, $newSchedules) {
		$newSchedules->each(function ($schedule) use ($existingSchedules) {
			$timeSchedules = $existingSchedules->where('date_time', $schedule->date_time);
			if ($existingSchedule = $timeSchedules->firstWhere('band_id', $schedule->band_id)) {
				if ($existingSchedule->payment != $schedule->payment || $existingSchedule->stage_id != $schedule->stage_id) {
					event(new ShowUpdated($schedule, $existingSchedule));
				}

				if ($existingSchedule->payment == $schedule->payment) {
					$schedule->approved = $existingSchedule->approved;
				}
			} else {
				event(new ShowCreated($schedule));
			}
			$schedule->save();
		});
	}
}
