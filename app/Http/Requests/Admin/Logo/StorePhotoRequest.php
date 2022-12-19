<?php

namespace App\Http\Requests\Admin\Logo;

use App\Models\BandMemberPhoto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Image;
use Storage;

class StorePhotoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Gate::allows('update-settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        return [
            'photo' => 'required|mimes:jpeg,bmp,png,gif,svg|clamav'
        ];
    }

    public function commit() {
        $photo = $this->file('photo');
        $image = Image::make($photo);
        Storage::put('public/images/logo.png', $image->encode());
        return ['logo updated'];
    }
}
