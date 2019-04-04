<?php

namespace App\Http\Controllers\Band;

use App\Http\Requests\Band\CreateSongRequest;
use App\Http\Requests\Band\DestroySongRequest;
use App\Http\Requests\Band\UpdateSongRequest;
use App\Models\Band;
use App\Models\BandSong;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SongController extends Controller {
	
	public function create(Band $band, CreateSongRequest $request) {
		return $request->commit();
	}
	
	public function update(UpdateSongRequest $request, Band $band, BandSong $song) {
		return $request->commit();
	}
	
	public function destroy(DestroySongRequest $request, Band $band, BandSong $song) {
		$request->commit();
		
		return [
			'success' => true
		];
	}
}
