<?php

namespace App\Http\Controllers;

use App\Models\BandAdmin;
use App\Models\BandAdminPhoto;
use App\Models\BandMemberPhoto;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\TaxReview;
use App\Models\WorkerPhoto;
use Crypt;
use finfo;
use Illuminate\Http\Request;
use Storage;

class PhotoController extends Controller {
	public function show(Photo $photo) {
		return Storage::response("public/photos/{$photo->file}");
	}
	
	public function worker(WorkerPhoto $photo) {
		$encryptedContents = Storage::get("public/photos/{$photo->file}");
		$decryptedContents = Crypt::decrypt($encryptedContents);
		
		if (pathinfo($photo->file, PATHINFO_EXTENSION)) {
			return response()->streamDownload(function () use ($decryptedContents) {
				echo $decryptedContents;
			}, $photo->file);
		}
		return response()->make($decryptedContents, 200, [
			'Content-Type' => 'image/jpeg'
		]);
	}
	
	public function bandMember(BandMemberPhoto $photo) {
		$encryptedContents = Storage::get("public/photos/{$photo->file}");
		$decryptedContents = Crypt::decrypt($encryptedContents);
		
		if (pathinfo($photo->file, PATHINFO_EXTENSION)) {
			return response()->streamDownload(function () use ($decryptedContents) {
				echo $decryptedContents;
			}, $photo->file);
		}
		return response()->make($decryptedContents, 200, [
			'Content-Type' => 'image/jpeg'
		]);
	}

	public function bandAdmin(BandAdminPhoto $photo ) {
		$encryptedContents = Storage::get("public/photos/{$photo->file}");
		$decryptedContents = Crypt::decrypt($encryptedContents);

		if (pathinfo($photo->file, PATHINFO_EXTENSION)) {
			return response()->streamDownload(function () use ($decryptedContents) {
				echo $decryptedContents;
			}, $photo->file);
		}
		return response()->make($decryptedContents, 200, [
			'Content-Type' => 'image/jpeg'
		]);
	}
	
	public function taxReview(TaxReview $taxReview) {
		$encryptedContents = Storage::get("public/taxReviews/{$taxReview->file}");
		$decryptedContents = Crypt::decrypt($encryptedContents);
		$extension = pathinfo($taxReview->file, PATHINFO_EXTENSION);
		$filename = str_replace(' ', '_', $taxReview->name) . ".{$extension}";
		
		return response()->streamDownload(function () use ($decryptedContents) {
			echo $decryptedContents;
		}, $filename);
	}
}
