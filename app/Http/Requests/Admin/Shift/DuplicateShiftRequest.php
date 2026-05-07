<?php

namespace App\Http\Requests\Admin\Shift;

use App\Models\Shift;
use App\Models\ShiftWorker;
use Illuminate\Foundation\Http\FormRequest;

class DuplicateShiftRequest extends FormRequest {

    private $originalShift;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $this->originalShift = $this->route('shift');
        return $this->user()->can('create', Shift::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            //
        ];
    }

    public function commit() {
        $newShift = new Shift;
        $newShift->date = $this->originalShift->date;
        $newShift->hours = $this->originalShift->hours;
        $newShift->workplace_id = $this->originalShift->workplace_id;
        $newShift->save();

        foreach ($this->originalShift->shiftWorkers as $shiftWorker) {
            $copy = new ShiftWorker;
            $copy->shift_id = $newShift->id;
            $copy->worker_id = null;
            $copy->work_function_id = $shiftWorker->work_function_id;
            $copy->start_time = $shiftWorker->start_time;
            $copy->end_time = $shiftWorker->end_time;
            $copy->save();
        }

        $newShift->name = $newShift->workplace->name;
        return $newShift;
    }
}
