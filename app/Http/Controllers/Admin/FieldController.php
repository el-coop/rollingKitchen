<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Fields\CreateFieldRequest;
use App\Http\Requests\Admin\Fields\DeleteFieldRequest;
use App\Http\Requests\Admin\Fields\EditFieldRequest;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller {

    public function index(){

    }

    public function create(CreateFieldRequest $request) {
        $field = $request->commit();
        return back();
    }

    public function edit(Field $field, EditFieldRequest $request) {
        $field = $request->commit();
        return back();
    }

    public function delete(Field $field, DeleteFieldRequest $request) {
        $request->commit();
        return back();
    }
}
