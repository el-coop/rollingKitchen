<?php

namespace App\Http\Controllers\Band;

use App\Http\Requests\Band\ApproveScheduleRequest;
use App\Http\Requests\Band\CreateBandMemberRequest;
use App\Http\Requests\Band\DestroyBandMemberRequest;
use App\Http\Requests\Band\RejectScheduleRequest;
use App\Http\Requests\Band\UpdateBandMemberRequest;
use App\Http\Requests\Band\UpdateBandRequest;
use App\Http\Requests\Band\UploadBandPdfRequest;
use App\Http\Requests\Band\UploadSetlistRequest;
use App\Models\Band;
use App\Models\BandSchedule;
use App\Models\Pdf;
use Auth;
use App;
use App\Models\BandMember;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Password;
use App\Http\Controllers\Controller;

class BandController extends Controller {

    use ResetsPasswords;

    public function showResetForm(Request $request, $token = null) {
        return view('worker.setPassword')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function show(Band $band) {
        $band->load('bandSongs', 'pdf', 'schedules.stage', 'setListFile');
        $locale = App::getLocale();
        $pdfs = Pdf::where('visibility', 3)->get();
        $message = app('settings')->get("bands_text_{$locale}");
        $privacyStatement = str_replace(PHP_EOL, '<br>', app('settings')->get("band_members_privacy_statement_{$locale}"));
        return view('band.band', compact('band', 'pdfs', 'message', 'privacyStatement'));
    }

    public function update(UpdateBandRequest $request, Band $band) {
        if ($band->payment_method == 'band' && $request->input('paymentMethod') == 'individual') {
            $toast = [
                'type' => 'success',
                'title' => __('band/band.createBandMemberToastTitle', [], $request->input('language')),
                'message' => __('band/band.createBandMemberToast', [], $request->input('language')),
                'position' => 'topCenter'
            ];
        } else {
            $toast = [
                'type' => 'success',
                'title' => '',
                'message' => __('vue.updateSuccess', [], $request->input('language'))
            ];
        }
        $request->commit();
        return back()->with('toast', $toast);
    }

    public function addBandMember(CreateBandMemberRequest $request, Band $band) {
        return $request->commit();
    }

    public function updateBandMember(UpdateBandMemberRequest $request, Band $band, BandMember $bandMember) {
        return $request->commit();
    }

    public function destroyBandMember(DestroyBandMemberRequest $request, Band $band, BandMember $bandMember) {
        $request->commit();

        return [
            'success' => true
        ];
    }

    public function approveSchedule(ApproveScheduleRequest $request, Band $band, BandSchedule $bandSchedule) {
        return $request->commit();
    }

    public function rejectSchedule(RejectScheduleRequest $request, Band $band, BandSchedule $bandSchedule) {
        return $request->commit();
    }

    public function showPdf(Band $band) {
        $pdf = $band->pdf;
        $filename = str_replace(' ', '_', $band->user->name . '_' . __('band/band.technicalRequirements'));
        $extension = pathinfo($pdf->file, PATHINFO_EXTENSION);
        return Storage::download("public/pdf/band/{$pdf->file}", "{$filename}.{$extension}");
    }

    public function showSetlist(Band $band) {
        $setlist = $band->setListFile;
        $filename = str_replace(' ', '_', $band->user->name . '_' . __('band/band.setList'));
        $extension = pathinfo($setlist->file, PATHINFO_EXTENSION);
        return Storage::download("public/pdf/band/{$setlist->file}", "{$filename}.{$extension}");
    }

    public function broker() {
        return Password::broker('workers');
    }


    public function redirectTo() {
        return Auth::user()->user->homePage();
    }

    public function uploadFile(UploadBandPdfRequest $request, Band $band) {
        $request->commit();
        return back()->with('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $request->input('language'))
        ]);
    }

    public function uploadSetlist(UploadSetlistRequest $request, Band $band) {
        $request->commit();
        return back()->with('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $request->input('language'))
        ]);
    }
}
