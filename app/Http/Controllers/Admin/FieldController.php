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
        return $field;
    }

    public function edit(Field $field, EditFieldRequest $request) {
        $field = $request->edit();
    }

    public function delete(Field $field, DeleteFieldRequest $request) {
        $request->delete();
        return back();
    }
}
