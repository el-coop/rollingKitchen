<?php

namespace App\Http\Requests\Kitchen\Photo;

use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Storage;
use Image;

class UploadPhotoRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchen = $this->route('kitchen');
		return $this->user()->can('update', $this->kitchen);
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
		$photo = new Photo;
		$photo->file = basename($path);
		$this->kitchen->photos()->save($photo);
		
		return $photo;
	}
	
	protected function processPhoto() {
		$photo = $this->file('photo');
		$image = Image::make($photo);
		$width = $image->width();
		$height = $image->height();
		if ($height > 500 || $width > 800) {
			$proportion = $height / $width;
			if ($proportion > 1) {
				$image->resize(round(500 / $proportion), 500);
			} else {
				$image->resize(800, round(800 * $proportion));
			}
		}
		$mime = $image->mime();
		$mime = str_replace('image/', '.', $mime);
		$hash = $photo->hashName();
		if ($mime != '.jpeg' || $mime != '.jpeg') {
			$hash = str_replace($mime, '.jpeg', $photo->hashName());
		}
		$path = 'public/photos/' . $hash;
		Storage::put($path, (string)$image->encode('jpeg'));
		return $path;
	}
}
