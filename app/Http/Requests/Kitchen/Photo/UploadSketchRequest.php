<?php

namespace App\Http\Requests\Kitchen\Photo;

use App\Models\ApplicationSketch;
use Illuminate\Foundation\Http\FormRequest;
use Storage;
use Image;

class UploadSketchRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() {
        $this->application = $this->route('application');
        return $this->user()->can('update', $this->application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'photo' => 'required|image|clamav'
        ];
    }

    public function commit() {
        $path = $this->processPhoto();
        $photo = new ApplicationSketch;
        $photo->file = basename($path);
        $this->application->sketches()->save($photo);

        return $photo;
    }

    protected function processPhoto() {
        $photo = $this->file('photo');
        $hash = $photo->hashName();
        $path = 'public/photos/' . $hash;

        if ($photo->extension() !== 'pdf') {
            $image = Image::make($photo);
            $width = $image->width();
            $height = $image->height();
            if ($height > 500 || $width > 500) {
                $proportion = $height / $width;
                if ($proportion > 1) {
                    $image->resize(round(500 / $proportion), 500);
                } else {
                    $image->resize(500, round(500 * $proportion));
                }
            }
            $mime = $image->mime();
            $mime = str_replace('image/', '.', $mime);
            if ($mime != '.jpeg' || $mime != '.jpeg') {
                $path = str_replace($mime, '.jpeg', $path);
            }
            Storage::put($path, encrypt((string)$image->encode('jpeg')));
        } else {
            Storage::put($path, encrypt($photo->get()));
        }
        return $path;
    }
}
