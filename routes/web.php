<?php

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

Auth::routes();


Route::get('/', 'PageController@index')->name('home');
Route::get('/home', 'PageController@index')->name('home');



Route::post('/rooms/messages/add', 'MessageController@messageAdd');
Route::get('/rooms/{roomId}/messages/{page}', 'MessageController@fetchRoomMessages');



Route::post('/rooms/add', 'RoomController@addRoom');
Route::get('/rooms/close/{roomId}', 'RoomController@closeRoom');