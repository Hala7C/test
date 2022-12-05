<?php

use App\Http\Controllers\API\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FilesController;
use App\Http\Controllers\API\FileOperationController;
use App\Http\Controllers\API\Display;
use App\Http\Controllers\API\SystemConfiguration;
use Spatie\FlareClient\Time\SystemTime;

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
Route::post('group/add/group', [GroupController::class, 'store'])->middleware('auth:sanctum');
Route::get('group', [GroupController::class, 'index'])->middleware('auth:sanctum');
Route::middleware([
    'auth:sanctum', 'check'

])->group(function () {

    Route::get('group/mamber/of/group/{id}', [GroupController::class, 'showMemberOfGroup']);
    Route::post('group/add/member/{id}', [GroupController::class, 'addMemberToGroup']);
    Route::get('group/show/files/{id}', [GroupController::class, 'showAllFilesInGroup']);
    Route::get('group/show/files/can/{id}', [GroupController::class, 'showAllFilesCanAdd']);
    Route::get('group/show/members/can/{id}', [GroupController::class, 'showMemberCanAdd']);
    Route::post('group/add/file/{id}', [GroupController::class, 'addFileToGroupe']);
    Route::delete('group/delete/from/group/{id}/{file_id}', [GroupController::class, 'deleteFileFromGroupe']);
    Route::delete('group/delete/group/{id}', [GroupController::class, 'deleteGroup']);
    Route::delete('group/delete/member/group/{id}/{member_id}', [GroupController::class, 'deleteMember']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'isUser',
])->group(function () {

    Route::get('/profile', [App\Http\Controllers\API\AuthController::class, 'profile']);
    Route::post('/profile/updatepassword', [App\Http\Controllers\API\AuthController::class, 'updatepassword']);
    Route::post('/profile/update', [App\Http\Controllers\API\AuthController::class, 'updateProfile']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'isUser',
])->group(function () {
    Route::post('file/create', [FilesController::class, 'storeDocument']);
    Route::delete('file/delete/{id}', [FilesController::class, 'destroyDocument']);

    Route::get('/file/read/{id}', [FileOperationController::class, 'readFile']);
    Route::post('/file/edit/{id}', [FileOperationController::class, 'editFile']);
    Route::get('/file/checkin/{id}', [FileOperationController::class, 'CheckIn']);
    Route::get('/file/checkout/{id}', [FileOperationController::class, 'CheckOut']);
    Route::post('/file/bulkCheckIn', [FileOperationController::class, 'bulkCheckIn']);
    Route::get('/myfiles', [Display::class, 'myFiles']);
    Route::get('/group/{id}/documents', [Display::class, 'documentsGroup']);
    Route::get('file/{id}/history', [Display::class, 'documentHisory']);
});
Route::post('/setting/connection', [SystemConfiguration::class, 'db_connection']);
