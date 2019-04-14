<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BandMember\CreateBandMemberRequest;
use App\Http\Requests\Admin\BandMember\DestroyBandMemberRequest;
use App\Http\Requests\Admin\BandMember\UpdateBandMemberRequest;
use App\Models\Band;
use App\Models\BandMember;
use App\Services\BandMemberService;
use Crypt;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use View;

class BandMemberController extends Controller {
	
	public function create(Band $band) {
		return (new BandMember)->fullData;
	}
	
	public function store(CreateBandMemberRequest $request, Band $band) {
		return $request->commit();
	}
	
	public function edit(Band $band, BandMember $bandMember) {
		return $bandMember->fullData;
	}
	
	public function update(UpdateBandMemberRequest $request, Band $band, BandMember $bandMember) {
		return $request->commit();
	}
	
	public function destroy(DestroyBandMemberRequest $request, Band $band, BandMember $bandMember) {
		$request->commit();
		return [
			'success' => true
		];
	}
	
	public function pdf(BandMember $bandMember, BandMemberService $bandMemberService) {
		$data = $bandMemberService->individual($bandMember);
		
		$images = $bandMember->photos->map(function ($photo) {
			$encryptedContents = Storage::get("public/photos/{$photo->file}");
			$decryptedContents = base64_encode(Crypt::decrypt($encryptedContents));
			
			return "data:image/jpg;base64,{$decryptedContents}";
		});
		$options = new Options();
		
		$options->set('isRemoteEnabled', true);
		$pdf = new Dompdf($options);
		$pdf->loadHtml(View::make('admin.bandMember.pdf', compact('data', 'images')));
		$pdf->render();
		
		return $pdf->stream("{$bandMember->user->name}.pdf");
	}
}
