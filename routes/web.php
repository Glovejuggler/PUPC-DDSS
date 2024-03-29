<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ActivityLogController;


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

Auth::routes(['register' => false]);

Route::get('/register', function (){
    return redirect()->back();
});

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

    Route::get('/trash', [FileController::class, 'trash_index'])->name('file.trash');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/password/change', [UserController::class, 'password_edit'])->name('password.edit');
    Route::put('profile/update', [UserController::class, 'profile_update'])->name('profile.update');
    Route::put('/change_password', [UserController::class, 'change_password'])->name('change_password');
    Route::post('/change_pfp', [AvatarController::class, 'store'])->name('avatar.change');

    Route::get('/folders', [FolderController::class, 'index'])->name('folder.index');
    Route::post('/folder/create', [FolderController::class, 'store'])->name('folder.store');
    Route::delete('/folder/delete/{folder}', [FolderController::class, 'destroy'])->name('folder.destroy');
    Route::get('/folder/{id}/files', [FolderController::class, 'show'])->name('folder.show');
    Route::get('/folder/recover/{id}', [FolderController::class, 'recover'])->name('folder.recover');
    Route::post('/folder/share/{id}', [FolderController::class, 'share'])->name('folder.share');
    Route::put('/folder/{id}/rename', [FolderController::class, 'update'])->name('folder.rename');

    Route::get('/files/{id?}', [FileController::class, 'index'])->name('file.index');
    Route::post('/file/create', [FileController::class, 'store'])->name('file.store');
    Route::get('file/download/{id}', [FileController::class, 'download'])->name('file.download');
    Route::delete('/file/delete/{file}', [FileController::class, 'destroy'])->name('file.destroy');
    Route::get('/file/recover/{id}', [FileController::class, 'recover'])->name('file.recover');
    Route::put('file/{file}/rename/update', [FileController::class, 'rename'])->name('file.rename');
    
    // Route::get('/file/{id}/share', [ShareController::class, 'share'])->name('share.file');
    Route::post('/share/{id}', [ShareController::class, 'create'])->name('share.sharefile');
    Route::get('/shared_files', [ShareController::class, 'index'])->name('share.index');
    Route::get('/share/view/{id}', [ShareController::class, 'show'])->name('share.view');

    Route::get('/search', [FileController::class, 'search'])->name('file.search');
    Route::get('/search_trash', [FileController::class, 'search_trash'])->name('trash.search');

    Route::get('/activities', [ActivityLogController::class, 'index'])->name('activity.log');
});