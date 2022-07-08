<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ShareController;


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
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// This checks if the email is already taken when adding a new user
Route::post('/emailcheck', [UserController::class, 'emailcheck'])->name('email.check');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/create', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/udpate/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
    Route::post('/role/create', [RoleController::class, 'store'])->name('role.store');
    Route::delete('/role/delete/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::get('roles/{id}/users', [RoleController::class, 'show'])->name('role.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/profile/edit', [UserController::class, 'profile_edit'])->name('profile.edit');
    Route::put('profile/update', [UserController::class, 'profile_update'])->name('profile.update');
    Route::put('/change_password', [UserController::class, 'change_password'])->name('change_password');

    Route::get('/folders', [FolderController::class, 'index'])->name('folder.index');
    Route::post('/folder/create', [FolderController::class, 'store'])->name('folder.store');
    Route::delete('/folder/delete/{folder}', [FolderController::class, 'destroy'])->name('folder.destroy');
    Route::get('/folder/{id}/files', [FolderController::class, 'show'])->name('folder.show');
    Route::get('/folder/recover/{id}', [FolderController::class, 'recover'])->name('folder.recover');

    Route::get('/files', [FileController::class, 'index'])->name('file.index');
    Route::post('/file/create', [FileController::class, 'store'])->name('file.store');
    Route::get('file/download/{id}', [FileController::class, 'download'])->name('file.download');
    Route::delete('/file/delete/{file}', [FileController::class, 'destroy'])->name('file.destroy');
    Route::get('/file/recover/{id}', [FileController::class, 'recover'])->name('file.recover');
    Route::get('file/{file}/rename', [FileController::class, 'edit'])->name('file.edit');
    Route::put('file/{file}/rename/update', [FileController::class, 'rename'])->name('file.rename');
    
    Route::get('/file/{id}/share', [ShareController::class, 'share'])->name('share.file');
    Route::post('/share/{id}', [ShareController::class, 'create'])->name('share.sharefile');
    Route::get('/shared_files', [ShareController::class, 'index'])->name('share.index');
});