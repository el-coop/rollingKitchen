<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Debtor\CreateDebtorRequest;
use App\Http\Requests\Admin\Debtor\DestroyDebtorRequest;
use App\Http\Requests\Admin\Debtor\UpdateDebtorRequest;
use App\Models\Debtor;
use Illuminate\Http\Request;

class DebtorController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$title = __('admin/debtors.debtors');
		$createTitle = __('admin/debtors.createDebtor');

		return view('admin.datatableWithNew', compact('title', 'createTitle'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return (new Debtor)->fulldata;
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CreateDebtorRequest $request
	 * @return void
	 */
	public function store(CreateDebtorRequest $request) {
		return $request->commit();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Debtor $debtor
	 * @return \Illuminate\Http\Response
	 */
	public function show(Debtor $debtor) {
		$indexLink = Debtor::indexPage();
		return view('admin.debtors.show', compact('debtor', 'indexLink'));
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Debtor $debtor
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Debtor $debtor) {
		return $debtor->fulldata;
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateDebtorRequest $request
	 * @param  \App\Models\Debtor $debtor
	 * @return
	 */
	public function update(UpdateDebtorRequest $request, Debtor $debtor) {
		return $request->commit();
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param DestroyDebtorRequest $request
	 * @param  \App\Models\Debtor $debtor
	 * @return void
	 */
	public function destroy(DestroyDebtorRequest $request, Debtor $debtor) {
		$request->commit();
	}
}
