<?php

namespace App\Http\Controllers;

use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\WorkerPhoto;
use Crypt;
use Illuminate\Http\Request;
use Storage;

class PhotoController extends Controller {
	public function show(Photo $photo) {
		return Storage::response("public/photos/{$photo->file}");
	}
	
	public function worker(WorkerPhoto $photo) {
		$encryptedContents = Storage::get("public/photos/{$photo->file}");
		$decryptedContents = Crypt::decrypt($encryptedContents);
		
		
		return response()->make($decryptedContents, 200, [
			'Content-Type' => 'image/jpeg'
		]);
	}
}
