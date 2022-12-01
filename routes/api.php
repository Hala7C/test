<?php

use App\Http\Controllers\API\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
Route::post('group/add/group', [GroupController::class, 'store']);
Route::get('group', [GroupController::class, 'index']);
Route::middleware([
    'auth:sanctum', 'check'

])->group(function () {

    Route::get('group/mamber/of/group/{id}', [GroupController::class, 'showMemberOfGroup']);
    Route::post('group/add/member/{id}', [GroupController::class, 'addMemberToGroup']);
    Route::get('group/show/files/{id}', [GroupController::class, 'showAllFilesInGroup']);
    Route::get('group/show/files/can/{id}', [GroupController::class, 'showAllFilesCanAdd']);
    Route::post('group/add/file/{id}', [GroupController::class, 'addFileToGroupe'])->name('add.file.group');
    Route::delete('group/delete/from/group/{id}/{file_id}', [GroupController::class, 'deleteFileFromGroupe'])->name('delete.file.group');
    Route::delete('group/delete/group/{id}', [GroupController::class, 'deleteGroup']);
});
