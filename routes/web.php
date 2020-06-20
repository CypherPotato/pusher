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

Route::post("/get", "PushController@GetPublicKeyContent")->name("api.key");
Route::post("/push", "PushController@Push")->name("api.push");
Route::get("/createPublicKey", "PushController@CreateKeyPair")->name("createKeyPair");
Route::get("/view", "PushController@ViewMessages")->name("ViewMessages");
Route::get("/view/createKey", "PushController@CreatePublicKeyView")->name("CreatePublicKey");
Route::get("/view/editKey", "PushController@EditPublicKeyView")->name("EditPublicKeyView");
Route::get("/view/deleteKey", "PushController@DeleteKeyPair")->name("DeleteKeyPair");
