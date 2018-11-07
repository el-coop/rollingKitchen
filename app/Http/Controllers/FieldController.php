<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fields\CreateFieldRequest;
use App\Http\Requests\Fields\DeleteFieldRequest;
use App\Http\Requests\Fields\EditFieldRequest;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function create(CreateFieldRequest $request){
        $field = $request->commit();
        return $field;
    }

    public function edit(Field $field,EditFieldRequest $request){
        $field = $request->edit();
    }

    public function delete(Field $field, DeleteFieldRequest $request){
        $request->delete();
    }
}
