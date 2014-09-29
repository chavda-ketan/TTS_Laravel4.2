<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/* Home */
Route::get('/', 'HomeController@index');

/* Twitter stuff */
Route::get('twitter', 'TwitterController@index');
Route::get('twitter/geodump', 'TwitterController@showLatLongForGeoSearch');


/* Customer records stuff */
Route::get('records','RecordsController@index');
Route::get('records/repeats','RecordsController@showMonthlyRepeat');
Route::get('records/repeats/weekly','RecordsController@showWeeklyRepeat');


/* Webmaster Tools stuff */
Route::get('wmt','WebmasterToolsController@index');


/* Customer mail stuff */
Route::get('mail', 'MailController@index');
Route::get('mail/dump', 'MailController@dump');
Route::get('mail/test', 'MailController@testEmail');
Route::get('mail/run', 'MailController@thankCustomers');
Route::get('mail/print', 'MailController@listThankedCustomers');


/* Send SMS Mail */
Route::get('sms/send', 'MailController@sendSMSEmail');
