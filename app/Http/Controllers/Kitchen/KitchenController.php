<?php

namespace App\Http\Controllers\Kitchen;

use App;
use App\Http\Requests\Kitchen\CreateKitchenRequest;
use App\Http\Requests\Kitchen\DestroyKitchenRequest;
use App\Http\Requests\Kitchen\Photo\UploadPhotoRequest;
use App\Http\Requests\Kitchen\UpdateKitchenRequest;
use App\Http\Requests\Kitchen\UsePastApplicationRequest;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\Photo;
use App\Models\Service;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KitchenController extends Controller {
    use ResetsPasswords;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateKitchenRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateKitchenRequest $request) {
        $kitchen = $request->commit();
        Auth::login($kitchen->user, true);
        return redirect()->action('Kitchen\KitchenController@edit', $kitchen);
    }

    public function storePhoto(Kitchen $kitchen, UploadPhotoRequest $request) {
        return $request->commit();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function show(Kitchen $kitchen) {
    }

    public function showPdf(Pdf $pdf) {
        return Storage::download("public/pdf/{$pdf->file}", "{$pdf->name}.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function edit(Kitchen $kitchen) {
        $kitchen->load('user', 'applications.products', 'applications.electricDevices', 'applications.services', 'photos');
        $locale = App::getLocale();

        $application = $kitchen->getCurrentApplication();
        $pastApplications = $kitchen->applications->where('year', '!=', app('settings')->get('registration_year'));

        if ($application->status === 'accepted') {
            $pdfs = Pdf::where('visibility', 1)->orWhere('visibility', 2)->orderBy('order')->get();
        } else {
            $pdfs = Pdf::where('visibility', 1)->orderBy('order')->get();
        }

        $services = Service::orderByRaw("LENGTH(name_{$locale}) desc")->get();
        $countableServices = $services->where('category', '!=', 'socket')->where('type', 0);
        $checkableServices = $services->where('category', '!=', 'socket')->where('type', 1);
        $sockets = $services->where('category', 'socket')->sortBy('price');

        $termsFile = Pdf::where("terms_and_conditions_{$locale}", true)->first();


        $message = app('settings')->get("application_text_{$locale}");
        if (!$application->isOpen()) {
            $message = app('settings')->get("application_success_text_{$locale}");
        }
        return view('kitchen.edit', compact('termsFile', 'kitchen', 'application', 'application', 'message', 'pastApplications', 'sockets', 'countableServices', 'checkableServices', 'pdfs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateKitchenRequest $request, Kitchen $kitchen) {
        $request->commit();
        return back()->with('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $request->input('language'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyKitchenRequest $request
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyKitchenRequest $request, Kitchen $kitchen) {
        $request->commit();
        return redirect()->action('HomeController@show');
    }

    public function destroyPhoto(Kitchen $kitchen, Photo $photo) {
        $photo->delete();
        return [
            'success' => true
        ];
    }

    public function showResetForm(Request $request, $token = null) {
        return view('worker.setPassword')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function broker() {
        return Password::broker('workers');
    }

    public function redirectTo() {
        return Auth::user()->user->homePage();
    }

    public function usePastApplication(UsePastApplicationRequest $request, Application $application) {
        $request->commit();
        return back()->with('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess', [], $request->input('language'))
        ]);
    }
}
