<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Http\Request;

class KitchenController extends Controller {
	
	public function index() {
		return view('admin.kitchens.index');
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	
	public function list(Request $request) {
		
		$kitchens = User::where('user_type', Kitchen::class)->join('kitchens', 'users.user_id', 'kitchens.id')->select('name','email','status');
		if ($request->filled('sort')) {
			$sort = explode('|', $request->input('sort'));
			$kitchens->orderBy($sort[0], $sort[1]);
		} else {
			$kitchens->orderBy('users.created_at','desc');
		}
		
		if ($request->filled('filter')) {
			foreach (json_decode($request->input('filter')) as $field => $filter) {
				if ($filter !== '') {
					$filterVal = "%{$filter}%";
					$kitchens->where($field, 'like', $filterVal);
				}
			}
		}
		
		return $kitchens->paginate($request->input('per_page'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		return true;
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function show(Kitchen $kitchen) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Kitchen $kitchen) {
		//
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Kitchen $kitchen) {
		//
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Kitchen $kitchen) {
		//
	}
}
