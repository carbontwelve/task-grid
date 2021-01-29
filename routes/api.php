<?php

use App\Http\Controllers\LoginController;
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

//
// Authentication endpoints
//

Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')
    ->group(function () {

        //
        // Workbook endpoints
        //

        Route::apiResource('workbook', WorkbookController::class);
        Route::get('workbook/{workbook}/worksheets', [WorksheetController::class, 'index'])
            ->name('workbook.worksheets');
        Route::post('workbook/{workbook}/worksheets', [WorksheetController::class, 'store'])
            ->name('worksheet.store');
        Route::patch('workbook/{workbook}/restore', [WorkbookController::class, 'restore'])
            ->name('workbook.restore');

        //
        // Worksheet endpoints
        //

        Route::apiResource('worksheet', WorksheetController::class)->except(['index', 'store']);
        Route::patch('worksheet/{worksheet}/restore', [WorksheetController::class, 'restore'])
            ->name('worksheet.restore');
        Route::post('worksheet/{worksheet}/milestones', [MilestoneController::class, 'store'])
            ->name('milestone.store');
        Route::get('worksheet/{worksheet}/milestones', [MilestoneController::class, 'index'])
            ->name('worksheet.milestones');
        Route::post('worksheet/{worksheet}/tasks', [TaskController::class, 'store'])
            ->name('task.store');
        Route::get('worksheet/{worksheet}/tasks', [TaskController::class, 'index'])
            ->name('worksheet.tasks');

        //
        // Milestone endpoints
        //

        Route::apiResource('milestone', MilestoneController::class)->except(['index', 'store']);
        Route::patch('milestone/{milestone}/restore', [MilestoneController::class, 'restore'])
            ->name('milestone.restore');

        //
        // Task endpoints
        //

        Route::apiResource('task', TaskController::class)->except(['index', 'store']);
        Route::patch('task/{task}/restore', [TaskController::class, 'restore'])
            ->name('task.restore');
        Route::put('task/{task}/milestone/{milestone}', [TaskController::class, 'milestone'])
            ->name('task.milestone');
    });
