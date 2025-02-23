<?php

use Devfaysal\BangladeshGeocode\Models\Union;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'test' => 'Hello World!',
//     ]);
// });

Route::get('/upazila/{districtId?}', function () {
    return Upazila::where('district_id', request('districtId'))->get();
});

Route::get('/union/{upazilaId?}', function () {
    return Union::where('upazila_id', request('upazilaId'))->get();
});

require __DIR__.'/custom.php';
require __DIR__.'/mehedi.php';
