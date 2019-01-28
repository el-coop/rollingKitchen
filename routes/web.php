<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/','HomeController@show');

Auth::routes([
	'register' => false
]);

Route::get('/language/{language}','LocaleController@set');
Route::get('/images/{photo}','PhotoController@show');
Route::get('/images/worker/{photo}','PhotoController@worker')->middleware(['auth','can:view,photo']);
Route::group(['middleware' => ['auth', 'userType:' . \App\Models\Admin::class]], function (){
	Route::get('datatable/list', 'DatatableController@list');
	Route::get('datatable/export', 'DatatableController@export');
});
Route::get('supervisorDatatable/list', 'DatatableController@supervisorList')->middleware(['auth', 'supervisor']);

foreach (\File::allFiles(__DIR__ . "/web") as $routeFile) {
    include $routeFile;
}

