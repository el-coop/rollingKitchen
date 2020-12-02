<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kitchen\CreateKitchenRequest;
use App\Http\Requests\Admin\Kitchen\DeleteKitchenRequest;
use App\Http\Requests\Admin\Kitchen\UpdateKitchenRequest;
use App\Http\Requests\Kitchen\DestroyKitchenRequest;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use function foo\func;
use Illuminate\Http\Request;

class KitchenController extends Controller {

    public function index() {
        $fieldType = 'Kitchen';
        $title = __('admin/kitchens.kitchens');
        $createTitle = __('admin/kitchens.createKitchen');
        $deleteButton = true;
        return view('admin.datatableWithNew', compact('fieldType', 'title', 'deleteButton', 'createTitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return (new Kitchen)->adminCreatedData;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateKitchenRequest $request) {
        return $request->commit();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function show(Kitchen $kitchen) {
        $locale = App::getLocale();

        $kitchen->load('photos', 'user', 'applications', 'applications.products', 'applications.invoices.payments', 'applications.electricDevices', 'applications.services');
        $indexLink = Kitchen::indexPage();
        $services = Service::orderByRaw("LENGTH(name_{$locale}) desc")->get();
        return view('admin.kitchens.show', compact('kitchen', 'indexLink', 'services'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function edit(Kitchen $kitchen) {

        return $kitchen->fullData;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Kitchen $kitchen
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateKitchenRequest $request, Kitchen $kitchen) {
        return $request->commit();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyKitchenRequest $request
     * @param \App\Models\Kitchen $kitchen
     * @return void
     */
    public function destroy(DestroyKitchenRequest $request, Kitchen $kitchen) {
        $request->commit();
    }

}
