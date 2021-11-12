<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function (){
    return redirect()->route('login');
});
Route::get('/home',  function (){
    return redirect()->route('index');
});

Auth::routes();

Route::get('/index', 'CarrosController@index')->name('index');
Route::view('/search', 'search')->name('view.search');
Route::post('/search', 'CarrosController@search')->name('search');
Route::delete('/car/delete/{id}', 'CarrosController@destroy')->name('carro.del');
