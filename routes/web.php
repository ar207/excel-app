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

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('upload/file', [\App\Http\Controllers\UploadFileController::class, 'uploadFile']);

    Route::get('fda', [\App\Http\Controllers\FdaFileController::class, 'fda']);
    Route::get('fda/files', [\App\Http\Controllers\FdaFileController::class, 'index']);
    Route::post('fda/files', [\App\Http\Controllers\FdaFileController::class, 'store']);
    Route::get('fda/data', [\App\Http\Controllers\FdaFileController::class, 'getData']);

    Route::get('odbc', [\App\Http\Controllers\ODBCController::class, 'index']);
    Route::get('odbc/data', [\App\Http\Controllers\ODBCController::class, 'getData']);
    Route::get('odbc/export', [\App\Http\Controllers\ODBCController::class, 'exportToExcel']);

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
Auth::routes();

