<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Logo\StorePhotoRequest;
use Illuminate\Http\Request;

class LogoController extends Controller {

    public function store(StorePhotoRequest $request) {
        return $request->commit();
    }
}
