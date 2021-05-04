<?php

use Illuminate\Support\Facades\Route;

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
})->name("home");


Route::post("/api/push", "PushController@Push")->name("api.push");
Route::get("/api/push", "PushController@GetMessage")->name("api.getmessage");
Route::patch("/api/push", "PushController@EditMessage")->name("api.editmessage");
Route::delete("/api/public_key", "PushController@DeleteKeyPair")->name("api.deletepublickey");
Route::post("/api/public_key", "PushController@CreateKeyPair")->name("createKeyPair");
Route::get("/api/public_key", "PushController@GetPublicKeyContent")->name("api.key");

Route::get("/patch_public_key", "PushController@CreateKeyPair")->name("createKeyPair");
Route::get("/view", "PushController@ViewMessages")->name("ViewMessages");
Route::get("/view/createKey", "PushController@CreatePublicKeyView")->name("CreatePublicKey");
Route::get("/view/editKey", "PushController@EditPublicKeyView")->name("EditPublicKeyView");
Route::get("/view/deleteKey", "PushController@DeleteKeyPair")->name("DeleteKeyPair");
Route::get("/messages", "PushController@MessagesComponent")->name("MessagesComponentCallback");

Route::post("/api/editmessage", "PushController@EditMessage")->name("api.editmessage");
Route::get("/deleteMessage", "PushController@DeleteMessage")->name("DeleteMessage");
Route::get('/editMessage', 'PushController@MessageEditor')->name("EditMessage");

Route::get('/telegrambot/assign', 'PushController@AssignTelegramBot')->name('assignTelegramBot');
Route::get('/telegrambot/delete', 'PushController@DeleteTelegramBot')->name('deleteTelegramBot');