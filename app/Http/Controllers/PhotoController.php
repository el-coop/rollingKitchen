<?php

namespace App\Http\Controllers;

use App\Models\Kitchen;
use App\Models\Photo;
use Illuminate\Http\Request;
use \File;
use Illuminate\Support\Facades\Storage;
use \Response;

class PhotoController extends Controller {
	public function show(Photo $photo) {
		return \Storage::response("public/photos/{$photo->file}");
		
	}
}
