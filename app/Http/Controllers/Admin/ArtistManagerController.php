<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ArtistManager\CreateArtistManagerRequest;
use App\Http\Requests\Admin\ArtistManager\DestroyArtistManagerRequest;
use App\Http\Requests\Admin\ArtistManager\UpdateArtistManagerRequest;
use App\Models\ArtistManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArtistManagerController extends Controller {

	public function index() {
		$title = __('admin/artists.artistManager');
		$createTitle = __('admin/artists.createArtistManager');
		return view('admin.datatableWithNew', compact('title', 'createTitle'));
	}

	public function create(){
		return (new ArtistManager)->fullData;
	}

	public function store(CreateArtistManagerRequest $request){
		return $request->commit();
	}

	public function edit(ArtistManager $artistManager){
		return $artistManager->fullData;
	}

	public function update(UpdateArtistManagerRequest $request, ArtistManager $artistManager){
		return $request->commit();
	}

	public function destroy(DestroyArtistManagerRequest $request, ArtistManager $artistManager){
		$request->commit();

		return [
			'success' => true
		];
	}
}
