<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Laravel\Jetstream\Rules\Role;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('file-upload', [FileController::class, 'store'])->name('file.store');

    Route::get('group', 'App\Http\Controllers\GroupController@index')->name('group');
    Route::get('group\show\member\{id}', 'App\Http\Controllers\GroupController@showMemberOfGroup')->name('group.show.member');
    Route::post('group\add\member\{group}', 'App\Http\Controllers\GroupController@addMemberToGroup')->name('add.member.group');
    Route::middleware('check')->get('group\show\{id}', 'App\Http\Controllers\GroupController@show')->name('group.show');
    Route::post('add\to\group\{group}', 'App\Http\Controllers\GroupController@addFileToGroupe')->name('add.file.group');
    Route::delete('delete\to\group\{group}\{id}', 'App\Http\Controllers\GroupController@deleteFileFromGroupe')->name('delete.file.group');
    Route::get('group/delete/member/group/{id}/{member_id}', 'App\Http\Controllers\GroupController@deleteMember');
    Route::get('file-upload', [FileController::class, 'create'])->name('file-upload');
    Route::post('file-upload', [FileController::class, 'store'])->name('file.store');
    Route::get('file-delete/{id}', [FileController::class, 'destroy'])->name('file.destroy');
});
