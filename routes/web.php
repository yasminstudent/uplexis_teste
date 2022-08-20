<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarsController;

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

// ----- ROTAS QUE NÃO REQUEREM AUTENTICAÇÃO -----
Route::redirect('/', '/login');
Route::redirect('/home', '/index');

Auth::routes();

// ----- ROTAS QUE REQUEREM AUTENTICAÇÃO -----
Route::middleware(['auth'])->group(function () {

    // Retorna views
    Route::get(
        '/index',
        [CarsController::class, 'index']
    )->name('index');

    Route::view(
        '/search', 
        'search'
    )->name('car.view.search');

    // Outras ações
    Route::post(
        '/search',
        [CarsController::class, 'search']
    )->name('car.search');

    Route::delete(
        '/car/delete/{id}',
        [CarsController::class, 'destroy']
    )->name('car.delete');
});
