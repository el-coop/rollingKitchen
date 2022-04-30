<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CheckInfoRequest;
use Illuminate\Http\Request;

class UserController extends Controller {


    public function checkInfo(CheckInfoRequest $request) {
        return $request->commit();
    }
}
