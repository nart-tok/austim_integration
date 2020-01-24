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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/get_events', 'events@getEvent');
Route::get('/update_events', 'events@updateEvent');
Route::get('/show_events', 'events@showEvents'); 
Route::get('/members_integration', 'events@memberIntegartion');
Route::get('/event_checker', 'events@eventChecker');
Route::post('/webhook', 'events@hook');
Route::get('/level-one-courses-update', 'events@levelOneCoursesUpdates');
Route::get('/mailchimp', 'events@mailchimp');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
