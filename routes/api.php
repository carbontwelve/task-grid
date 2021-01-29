<?php

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

Route::middleware('auth:api')
    ->namespace('\\App\\Http\\Controllers')
    ->group(function () {

        //
        // Workbook endpoints
        //

        Route::apiResource('workbook', 'WorkbookController');
        Route::get('workbook/{workbook}/worksheets', 'WorksheetController@index')
            ->name('workbook.worksheets');
        Route::post('workbook/{workbook}/worksheets', 'WorksheetController@store')
            ->name('worksheet.store');
        Route::patch('workbook/{workbook}/restore', 'WorkbookController@restore')
            ->name('workbook.restore');

        //
        // Worksheet endpoints
        //

        Route::apiResource('worksheet', 'WorksheetController')->except(['index', 'store']);
        Route::patch('worksheet/{worksheet}/restore', 'WorksheetController@restore')
            ->name('worksheet.restore');
        Route::post('worksheet/{worksheet}/milestones', 'MilestoneController@store')
            ->name('milestone.store');
        Route::get('worksheet/{worksheet}/milestones', 'MilestoneController@index')
            ->name('worksheet.milestones');
        Route::post('worksheet/{worksheet}/tasks', 'TaskController@store')
            ->name('task.store');
        Route::get('worksheet/{worksheet}/tasks', 'TaskController@index')
            ->name('worksheet.tasks');

        //
        // Milestone endpoints
        //

        Route::apiResource('milestone', 'MilestoneController')->except(['index', 'store']);
        Route::patch('milestone/{milestone}/restore', 'MilestoneController@restore')
            ->name('milestone.restore');

        //
        // Task endpoints
        //

        Route::apiResource('task', 'TaskController')->except(['index', 'store']);
        Route::patch('task/{task}/restore', 'TaskController@restore')
            ->name('task.restore');
        Route::put('task/{task}/milestone/{milestone}', 'TaskController@milestone')
            ->name('task.milestone');
    });
