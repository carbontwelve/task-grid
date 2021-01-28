<?php

use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkbookController;
use App\Http\Controllers\WorksheetController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function() {
    Route::apiResource('workbook', WorkbookController::class);
    Route::get('workbook/{workbook}/worksheets', 'App\Http\Controllers\WorksheetController@index')
        ->name('workbook.worksheets');
    Route::post('workbook/{workbook}/worksheets', 'App\Http\Controllers\WorksheetController@store')
        ->name('worksheet.store');
    Route::patch('workbook/{workbook}/restore', 'App\Http\Controllers\WorkbookController@restore')
        ->name('workbook.restore');
    Route::apiResource('worksheet', WorksheetController::class)->except(['index', 'store']);
    Route::patch('worksheet/{worksheet}/restore', 'App\Http\Controllers\WorksheetController@restore')
        ->name('worksheet.restore');
    Route::post('worksheet/{worksheet}/milestones', 'App\Http\Controllers\MilestoneController@store')
        ->name('milestone.store');
    Route::get('worksheet/{worksheet}/milestones', 'App\Http\Controllers\MilestoneController@index')
        ->name('worksheet.milestones');
    Route::get('worksheet/{worksheet}/tasks', 'App\Http\Controllers\TaskController@index')
        ->name('worksheet.tasks');
    Route::apiResource('milestone', MilestoneController::class)->except(['index', 'store']);
    Route::patch('milestone/{milestone}/restore', 'App\Http\Controllers\MilestoneController@restore')
        ->name('milestone.restore');
    Route::apiResource('task', TaskController::class)->except(['index']);

});
