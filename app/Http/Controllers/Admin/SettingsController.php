<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Settings\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller {

    public function show(){
        $settings = Setting::all()->sortBy('name');
        return view('admin.settings.show', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request){
        $request->commit();
        return redirect()->back();
    }
}
