<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BandMember\CreateBandMemberRequest;
use App\Http\Requests\Admin\BandMember\DestroyBandMemberRequest;
use App\Http\Requests\Admin\BandMember\UpdateBandMemberRequest;
use App\Models\Band;
use App\Models\BandMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BandMemberController extends Controller {

	public function create(Band $band){
		return (new BandMember)->fullData;
	}

	public function store(CreateBandMemberRequest $request, Band $band){
		return $request->commit();
	}

	public function edit(Band $band, BandMember $bandMember){
		return $bandMember->fullData;
	}

	public function update(UpdateBandMemberRequest $request, Band $band, BandMember $bandMember) {
		return $request->commit();
	}

	public function destroy(DestroyBandMemberRequest $request, Band $band, BandMember $bandMember){
		$request->commit();
		return [
		'success' => true
		];
	}
}
