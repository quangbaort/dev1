<?php

use App\Http\Controllers\Api\Company;
use App\Http\Controllers\Api\CountNotifyUnAnswer;
use App\Http\Controllers\Api\Department;
use App\Http\Controllers\Api\Events;
use App\Http\Controllers\Api\FileManagement;
use App\Http\Controllers\Api\Holiday;
use App\Http\Controllers\Api\Log;
use App\Http\Controllers\Api\Memo;
use App\Http\Controllers\Api\Menu;
use App\Http\Controllers\Api\News;
use App\Http\Controllers\Api\NotifyGroup;
use App\Http\Controllers\Api\Organization;
use App\Http\Controllers\Api\Prefecture;
use App\Http\Controllers\Api\Role;
use App\Http\Controllers\Api\SafetyCheck;
use App\Http\Controllers\Api\SafetyCheckNotifyReply;
use App\Http\Controllers\Api\User;
use App\Http\Controllers\Auth\Api\LoginController;
use App\Http\Controllers\Auth\Api\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/forgot-password', [ResetPasswordController::class, 'sendMail'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
|
*/
Route::prefix('v1/global')->group(function () {
    Route::get('/prefectures', [Prefecture::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| ALL API
|--------------------------------------------------------------------------
|
*/

Route::get('/safeties/answer', [SafetyCheckNotifyReply::class, 'answerSafety']);


Route::prefix('v1')->middleware(['auth:api'])->group(function () {

    //Role
    Route::apiResource('roles', Role::class)->only(['index']);


    //Organization Management
    Route::apiResource('organizations', Organization::class)->only([
        'index', 'show', 'store', 'destroy'
    ])->middleware('can:system-admin')->whereUuid('organization');

    Route::prefix('organizations')->middleware('can:system-admin')->group(function () {
        //delete multiple organizations
        Route::delete('/delete-multiple', [Organization::class, 'deleteMultiple']);
        //export organizations
        Route::post('/export-csv', [Organization::class, 'exportCSV']);
        //check organizations
        Route::get('/check-organization', [Organization::class, 'checkOrganization']);
    });


    Route::middleware('organ')->group(function () {

        Route::get('/total-unanswered-notify', [CountNotifyUnAnswer::class, 'index']);
        Route::get('/profile', [User::class, 'profile']);
        Route::get('/storage-free', [FileManagement::class, 'storageFree'])->middleware('admin.higher');

        //TODO: Refactor
        Route::prefix('users')->controller(User::class)->group(function () {
            Route::get('/', 'index')->middleware('admin.higher');
            Route::post('/', 'store');
            Route::get('/{id}', 'show')->whereUuid('id');
            Route::delete('/{id}', 'destroy')->whereUuid('id')->middleware('admin.higher');

            // check email user
            Route::get('/check-email', 'checkEmail')->middleware('admin.higher');

            // check limit user of organization
            Route::get('/check-count-users', [User::class, 'createAccountLimited'])->middleware('admin.higher');

            //delete multiple users
            Route::post('/delete-multiple', 'deleteMultiple')->middleware('admin.higher');

            //export users
            Route::post('/export-csv', [User::class, 'exportUser'])->middleware('admin.higher');

            // import users
            Route::post('/import-csv', [User::class, 'importUser'])->middleware('admin.higher');
        });

        //Company Management
        Route::apiResource('companies', Company::class)->only([
            'index', 'show', 'store', 'destroy'
        ])->whereUuid('company');
        Route::delete('companies/destroyMulti', [Company::class, 'destroyMulti'])->name('companies.destroyMulti');
        Route::post('companies/exportCsv', [Company::class, 'exportCsv']);

        //Department Management
        Route::apiResource('departments', Department::class)->only([
            'index', 'show', 'store', 'destroy'
        ])->whereUuid('department');
        Route::get('departments/tree', [Department::class, 'tree'])->name('departments.tree');
        Route::get('departments/all', [Department::class, 'all'])->name('departments.all');
        Route::get('departments/updateDispOrder', [Department::class, 'updateDispOrder']);

        Route::delete('departments/destroyMulti', [Department::class, 'destroyMulti'])
            ->name('departments.destroyMulti');

        //Holiday
        Route::apiResource('holidays', Holiday::class)->only([
            'index', 'show', 'store', 'destroy'
        ])->whereUuid('holiday');
        Route::delete('holidays/destroyMulti', [Holiday::class, 'destroyMulti'])->name('holidays.destroyMulti');
        Route::post('holidays/exportCsv', [Holiday::class, 'exportCsv']);

        //Memo
        Route::apiResource('memos', Memo::class)->only([
            'index', 'show', 'store', 'destroy'
        ])->whereUuid('memo');
        Route::delete('memos/destroyMulti', [Memo::class, 'destroyMulti'])->name('memos.destroyMulti');

        //Log
        Route::apiResource('logs', Log::class)->only([
            'index', 'show', 'store', 'destroy',
        ])->whereUuid('log')->middleware('can:system-admin');
        Route::prefix('logs')->group(function () {
            Route::delete('/destroyMulti', [Log::class, 'destroyMulti'])->middleware('can:system-admin');
        });

        Route::prefix('safeties')->middleware(['enable:safety_check'])->controller(SafetyCheck::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');

                Route::get('/{id}', 'show')->whereUuid('id');
                Route::delete('/{id}', 'destroy')->whereUuid('id');
                Route::get('/{id}/chart', 'chart')->whereUuid('id');
                Route::post('/{id}/answer', 'answer')->whereUuid('id');
                Route::post('/{id}/response-answer', 'responseAnswer')->whereUuid('id');
                Route::post('/{id}/remind-answer', 'remindAnswer')->whereUuid('id')->middleware('admin.higher');
                Route::post('/delete-multiple', 'deleteMultiple');
            });

        //News
        Route::prefix('notifies')->controller(News::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');

            Route::get('/{id}', 'show')->whereUuid('id');
            Route::delete('/{id}', 'destroy')->whereUuid('id')->middleware('admin.higher');
            Route::get('{id}/chart', 'chart')->whereUuid('id')->middleware('admin.higher');
            Route::get('{id}/users', 'newsResponses')->whereUuid('id')->middleware('admin.higher');
            Route::get('{id}/resend-remind', 'resendRemind')->whereUuid('id');
        });

        Route::delete('notify/destroyMulti', [News::class, 'destroyMulti']);

        //Notify Group
        Route::apiResource('notify-groups', NotifyGroup::class)->only([
            'index', 'show', 'store', 'destroy'
        ])->whereUuid('notifyGroup');
        Route::delete('notify-group/destroyMulti', [NotifyGroup::class, 'destroyMulti']);
        Route::get('notify-group/updateDispOrder', [NotifyGroup::class, 'updateDispOrder']);

        // get menu
        Route::get('/menu', [Menu::class , 'index']);

        /**
         * Event
         */
        Route::prefix('events')->controller(Events::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show')->whereUuid('id');
            Route::delete('/{id}', 'destroy')->whereUuid('id');
            Route::get('/{id}/chart', 'chart')->whereUuid('id');
            Route::post('/{id}/response/{answer}', 'userResponse')->whereUuid('id');
            Route::get('/{id}/remind-answer', 'remindAnswerInvited')->whereUuid('id')->middleware('admin.higher');
        });

        /**
         * File management
         */
        Route::prefix('file-managements')->controller(FileManagement::class)->group(function () {
            Route::get('/files/{type}/{folder?}', 'files');
            Route::post('/files', 'upload');
            Route::delete('/files/{id}', 'destroyFile');

            Route::get('/folders/{type}', 'folders');
            Route::post('/folders', 'makeDir');
            Route::patch('/folders', 'renameFolder');
            Route::delete('/folders/{id}', 'destroyFolder');
        });
    });
});
