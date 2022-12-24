<?php

namespace App\Providers;

use App\Events\EventCreatedEvent;
use App\Events\FileDeletedEvent;
use App\Events\FileUploadedEvent;
use App\Events\NewsCreatedEvent;
use App\Events\NewsUpdatedEvent;
use App\Events\OrganAttachedUserEvent;
use App\Events\RemindAnswerSafetyEvent;
use App\Events\RemindConfirmAttendEvent;
use App\Events\ResendRemindNewsEvent;
use App\Events\SafetyCheckCreatedEvent;
use App\Events\SafetyCheckUpdatedEvent;
use App\Events\UserDetachedOrganEvent;
use App\Listeners\EraseUserDataListener;
use App\Listeners\ResizeStorageLimitListener;
use App\Listeners\SafetyCheckSendNotifyListener;
use App\Listeners\SaveActivityLogListener;
use App\Listeners\SaveUploadedFileListener;
use App\Listeners\SendNoticeNewsListener;
use App\Listeners\SendNotifyInviteToEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrganAttachedUserEvent::class => [
            SaveActivityLogListener::class
        ],
        UserDetachedOrganEvent::class => [
            EraseUserDataListener::class,
            SaveActivityLogListener::class
        ],
        /**
         * File management
         */
        FileUploadedEvent::class => [
            SaveUploadedFileListener::class,
            ResizeStorageLimitListener::class,
        ],
        FileDeletedEvent::class => [
            ResizeStorageLimitListener::class,
        ],
        /**
         * Events
         */
        EventCreatedEvent::class  => [
            SendNotifyInviteToEvent::class,
        ],
        RemindConfirmAttendEvent::class => [
            SendNotifyInviteToEvent::class,
        ],
        /**
         * News
         */
        NewsCreatedEvent::class => [
            SendNoticeNewsListener::class
        ],
        NewsUpdatedEvent::class => [
        ],
        ResendRemindNewsEvent::class => [
            SendNoticeNewsListener::class
        ],
        /**
         * Safety check
         */
        SafetyCheckCreatedEvent::class => [
            SafetyCheckSendNotifyListener::class
        ],
        SafetyCheckUpdatedEvent::class => [
            SafetyCheckSendNotifyListener::class
        ],
        RemindAnswerSafetyEvent::class => [
            SafetyCheckSendNotifyListener::class
        ],
    ];
}
