<?php

use Illuminate\Http\Request;

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

Route::get('/', function () {
    return app()->version();
});
//下载或查看文件
Route::get('/file/{id}.{ext}', [
    'middleware' => 'auth_sign',
    'uses'       => 'FileController@index',
]);
//上传文件
Route::post('/file', [
    'middleware' => 'auth_sign',
    'uses'       => 'FileController@create',
]);
//删除文件
Route::delete('/file/{id}.{ext}', [
    'middleware' => 'auth_sign',
    'uses'       => 'FileController@destroy',
]);
