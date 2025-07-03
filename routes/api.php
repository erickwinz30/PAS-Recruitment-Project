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

//dashboard
Route::get('/dashboard', 'DashboardController@apiFetch');

//stocks
Route::get('/stocks', 'StockController@apiIndex');

//request approvals
Route::get('/request-approval', 'RequestApprovalController@apiIndex');
Route::get('/request-approval/approved', 'RequestApprovalController@apiRequestApproved');
Route::get('/request-approval/rejected', 'RequestApprovalController@apiRequestRejected');
Route::get('/request-approval/all', 'RequestApprovalController@apiRequestAll');

//users
Route::get('/users', 'UserController@apiIndex');
Route::get('/users/{user}/edit', 'UserController@apiEdit');
