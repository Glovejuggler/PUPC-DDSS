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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    Route::post('/user/create', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('user.show');
    Route::get('/user/{id}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/udpate/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');

    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
    Route::post('/role/create', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
    Route::delete('/role/delete/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy');
    Route::get('roles/{id}/users', [App\Http\Controllers\RoleController::class, 'show'])->name('role.show');

    Route::get('/folders', [App\Http\Controllers\FolderController::class, 'index'])->name('folder.index');
    Route::post('/folder/create', [App\Http\Controllers\FolderController::class, 'store'])->name('folder.store');
    Route::delete('/folder/delete/{folder}', [App\Http\Controllers\FolderController::class, 'destroy'])->name('folder.destroy');
    Route::get('/folder/{id}/files', [App\Http\Controllers\FolderController::class, 'show'])->name('folder.show');
    Route::get('/folder/recover/{id}', [App\Http\Controllers\FolderController::class, 'recover'])->name('folder.recover');

    Route::get('/files', [App\Http\Controllers\FileController::class, 'index'])->name('file.index');
    Route::post('/file/create', [App\Http\Controllers\FileController::class, 'store'])->name('file.store');
    Route::delete('/file/delete/{file}', [App\Http\Controllers\FileController::class, 'destroy'])->name('file.destroy');
    Route::get('/file/recover/{id}', [App\Http\Controllers\FileController::class, 'recover'])->name('file.recover');
});