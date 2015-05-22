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
Route::post('twitter/add', 'TwitterController@addSearchQuery');
Route::get('twitter/delete', 'TwitterController@removeSearchQuery');

/* Customer records stuff */
Route::get('records', 'RecordsController@index');
Route::get('records/repeats', 'RecordsController@showRepeatCustomerReport');
Route::get('records/repeats/debug', 'RecordsController@debug');
Route::get('lookup', 'RecordsController@customerLookup');

/* Landing page records */
Route::get('records/landing', 'RecordsController@showLandingPageReport');
Route::post('records/landing', 'RecordsController@showLandingPageReport');

Route::get('records/landing/aggregate', 'RecordsController@showLandingPageAggregateReport');
Route::post('records/landing/aggregate', 'RecordsController@showLandingPageAggregateReport');

/* Delta report */
Route::get('records/delta', 'RecordsController@showDeltaReport');

/* Webmaster Tools stuff */
Route::get('wmt', 'WebmasterToolsController@index');

/* Customer mail stuff */
Route::get('mail', 'MailController@index');
Route::get('mail/test', 'MailController@testEmail');
Route::get('mail/run', 'MailController@thankCustomers');
Route::get('mail/print', 'MailController@listThankedCustomers');
Route::get('mail/form', 'MailController@listForms');
Route::get('mail/business/dump', 'MailController@getBusinessEmails');

/* Send SMS Mail */
Route::get('sms/send', 'MailController@sendSMSEmail');

/* Supplier shit */
Route::get('supplier', 'InventoryController@supplierForm');
Route::post('supplier', 'InventoryController@addSupplier');

/* Inventory Management */

Route::get('inventory', 'InventoryController@inventoryTransfer');
Route::get('inventory/department', 'InventoryController@inventoryTransferCategories');
Route::get('inventory/search', 'InventoryController@inventoryTransferSearch');

Route::get('inventory/queue', 'InventoryController@inventoryTransferQueue');











/* inline simple shit and ajax shit and SHIT FUCK SHIT SHIT COCK ASS MOTHERFUCKER */
Route::get('shit/track', 'ShitController@toggleTrack');
Route::post('shit/range/add', 'ShitController@rangeAdd');
Route::post('shit/range/del', 'ShitController@rangeDel');
Route::get('shit/range/daily', 'ShitController@showRangeDailyData');

/* ASS */
Route::get('battery', 'ShitController@niggersStink');
Route::get('screen', 'ShitController@hitlerDidNothingWrong');
Route::get('screen2', 'ShitController@iCantBreathe');
Route::get('screen3', 'ShitController@chrisBenoit');
Route::get('fixparents', 'ShitController@fixParents');

Route::get('noseries', 'ShitController@nancyBenoit');
Route::get('noseries2', 'ShitController@danielBenoit');
Route::get('noseries3', 'ShitController@enclosedPoolArea');

Route::get('phonebattery', 'ShitController@phoneBatteries');
Route::get('toners', 'ShitController@tonerCartridges');

Route::get('laptopbattery', 'ShitController@batterySeries');
Route::get('laptopbattery2', 'ShitController@batteryModels');
