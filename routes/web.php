<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Events;
use App\Http\Controllers\Web\News;
use App\Http\Controllers\Web\SafetyCheck;

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
    return view('application');
});

/**
 * Event
 */
Route::get('/events/invited/response', [Events::class, 'inviteAnswer'])
    ->middleware('signed')->name(EVENT_RESPONSE_INVITE_ROUTE);

Route::get('/news/mark-as-read', [News::class, 'markAsRead'])->middleware('signed')->name(NEWS_MARK_READ_ROUTE);

Route::get('/safety-check/{id}/answer', [SafetyCheck::class, 'answer'])
    ->middleware('signed')->name(SAFETY_ANSWER_ROUTE);

Route::get('files/download', function (Request $request) {
    return \Illuminate\Support\Facades\Storage::download($request->get('path'));
})->middleware('signed')->name(FILE_DOWNLOAD);

Route::view('/{any}', 'application')
    ->where('any', '.*');
