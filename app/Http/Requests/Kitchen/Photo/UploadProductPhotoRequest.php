<?php

namespace App\Http\Requests\Kitchen\Photo;

use App\Models\ProductPhoto;
use Illuminate\Foundation\Http\FormRequest;
use Storage;
use Image;

class UploadProductPhotoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $this->application = $this->route('application');
        return $this->user()->can('update', $this->application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {
        return [
            'photo' => 'required|image|clamav'
        ];
    }

    public function commit() {
        $product = $this->route('product');
        $path = $this->processPhoto();
        $photo = new ProductPhoto();
        $photo->file = basename($path);
        $product->photos()->save($photo);

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
            if ($height > 800 || $width > 500) {
                $proportion = $height / $width;
                if ($proportion > 1) {
                    $image->resize(round(500 / $proportion), 500);
                } else {
                    $image->resize(800, round(800 * $proportion));
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
