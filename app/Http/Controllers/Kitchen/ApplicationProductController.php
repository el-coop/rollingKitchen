<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Requests\Kitchen\CreateProductRequest;
use App\Http\Requests\Kitchen\UpdateProductRequest;
use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationProductController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Application $application
	 * @param CreateProductRequest $request
	 * @return array
	 */
	public function create(Application $application, CreateProductRequest $request) {
		return $request->commit();
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param Application $application
	 * @return void
	 */
	public function show(Application $application) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Product $product
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Product $product) {
		//
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateProductRequest $request
	 * @param Application $application
	 * @param  \App\Models\Product $product
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateProductRequest $request, Application $application, Product $product) {
		return $request->commit();
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Product $product
	 * @return array
	 * @throws \Exception
	 */
	public function destroy(Application $application, Product $product) {
		$product->delete();
		return [
			'success' => true
		];
	}

    public function storePhoto() {
        
    }
}
