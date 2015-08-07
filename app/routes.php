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

/* High score chart */
Route::get('records/top', 'RecordsController@sortarray');


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

Route::get('inventory', 'InventoryController@inventoryTransferList');
Route::get('inventory/create', 'InventoryController@inventoryTransferCreate');

Route::get('inventory/details/{id}', 'InventoryController@inventoryTransferView');
Route::get('inventory/details/{id}/edit', 'InventoryController@inventoryTransferEdit');
Route::get('inventory/details/{id}/cancel', 'InventoryController@inventoryTransferCancel');
Route::get('inventory/details/{id}/reopen', 'InventoryController@inventoryTransferReopen');
Route::post('inventory/details/{id}/complete', 'InventoryController@inventoryTransferComplete');


Route::get('inventory/department', 'InventoryController@inventoryTransferCategories');
Route::get('inventory/search', 'InventoryController@inventoryTransferSearch');
Route::post('inventory/save', 'InventoryController@inventoryTransferSave');
Route::post('inventory/details/{id}/update', 'InventoryController@inventoryTransferUpdate');

Route::get('inventory/adjustment', 'InventoryController@inventoryAdjustmentLog');

Route::get('inventory/test/{id}', 'InventoryController@inventoryTransferTest');


/* Account statements */
Route::get('statement/test', 'StatementController@generateStatement');
Route::get('statement/invoice', 'StatementController@invoice');
Route::get('statement/overdue', 'StatementController@overdue');
Route::get('statement/blob', 'StatementController@blobTest');
Route::get('statement/receipt/{customer}/{transaction}','StatementController@generateReceipt');


Route::get('statement/cron','StatementController@accountStatementCron');


/* Model breakdown */
Route::get('records/breakdown/dump', 'RecordsController@testBreakdown');
Route::get('records/breakdown', 'RecordsController@iPhoneBreakdown');




/* inline simple shit and shit */
Route::get('shit/track', 'ShitController@toggleTrack');
Route::post('shit/range/add', 'ShitController@rangeAdd');
Route::post('shit/range/del', 'ShitController@rangeDel');
Route::get('shit/range/daily', 'ShitController@showRangeDailyData');




/* ASS */
Route::get('battery', 'ShitController@battery');
Route::get('screen', 'ShitController@laptopScreenBrands');
Route::get('screen2', 'ShitController@laptopScreenSeries');
Route::get('screen3', 'ShitController@laptopScreens');
Route::get('fixparents', 'ShitController@fixParents');

Route::get('noseries', 'ShitController@laptopScreensNoSeries');
Route::get('noseries2', 'ShitController@laptopScreensNoSeries2');
Route::get('noseries3', 'ShitController@laptopScreensNoSeries3');



Route::get('phonebattery', 'ShitController@phoneBatteries');
Route::get('toners', 'ShitController@tonerCartridges');

Route::get('laptopbattery', 'ShitController@batterySeries');
Route::get('laptopbattery2', 'ShitController@batteryModels');
