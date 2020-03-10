<?php

namespace App\Http\Requests\Band;

use App\Models\SetListFile;
use Illuminate\Foundation\Http\FormRequest;

class UploadSetlistRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $this->band = $this->route('band');
        return $this->user()->can('update', $this->band);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'owned' => 'required|in:yes,no,both',
            'protected' => 'required|in:yes,no,both',
            'file' => 'required|file|clamav'
        ];
    }

    public function commit() {
        $setlist = new SetListFile;
        $path = $this->file('file')->store('public/pdf/band');
        $setlist->file = basename($path);
        if ($this->band->setListFile){
            $this->band->setListFile->delete();
        }

        $setlist->owned = $this->get('owned');
        $setlist->protected = $this->get('protected');
        $this->band->setListFile()->save($setlist);
        return $setlist;
    }
}
