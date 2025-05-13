<?php

namespace App\Http\Requests\Worker;

use App\Models\WorkerPhoto;
use Illuminate\Foundation\Http\FormRequest;
use Image;
use Storage;

class StorePhotoRequest extends FormRequest {
	/**
	 * @var \Illuminate\Routing\Route|object|string
	 */
	private $worker;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->worker = $this->route('worker');
		return $this->user()->can('update', $this->worker);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'photo' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf|clamav'
		];
	}

	public function commit() {
        $path = $this->processPhoto();
		$photo = new WorkerPhoto();
		$photo->file = basename($path);
		$this->worker->photos()->save($photo);

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
