<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Settings\UpdateSettingsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller {

    public function show(){
        $settings = app('settings')->all();
        return view('admin.settings.show', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request){
        $request->commit();
        return redirect()->back();
    }
}
