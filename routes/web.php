<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ZohoSignController;

Route::get('/send-document', [ZohoSignController::class, 'sendDocument']);
Route::get('/send-document-to-multiple-clients', [ZohoSignController::class, 'sendDocumentMultipleRecipients']);



Route::get('/dynamic-multiple-form', [ZohoSignController::class, 'showDynamicForm'])->name('zoho.dynamic.form');
Route::post('/send-dynamic-recipients', [ZohoSignController::class, 'sendToMultipleRecipients'])->name('zoho.send.dynamic');



Route::get('/zoho/documents', [ZohoSignController::class, 'listSentDocuments']);
Route::get('/zoho/document/{requestId}', [ZohoSignController::class, 'getDocumentDetails']);
Route::post('/zoho/document/{requestId}/cancel', [ZohoSignController::class, 'cancelDocument']);
Route::get('/zoho/document/{requestId}/download', [ZohoSignController::class, 'downloadSignedDocument']);