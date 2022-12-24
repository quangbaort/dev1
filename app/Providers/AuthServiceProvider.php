<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Log;
use App\Models\Memo;
use App\Models\Organization;
use App\Models\SafetyCheck;
use App\Models\SafetyCheckResponse;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EventPolicy;
use App\Policies\HolidayPolicy;
use App\Policies\LogPolicy;
use App\Policies\MemoPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\SafetyCheckNotifyReplyPolicy;
use App\Policies\SafetyCheckPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class                => UserPolicy::class,
        Organization::class        => OrganizationPolicy::class,
        Department::class          => DepartmentPolicy::class,
        Holiday::class             => HolidayPolicy::class,
        Event::class               => EventPolicy::class,
        Log::class                 => LogPolicy::class,
        Memo::class                => MemoPolicy::class,
        SafetyCheck::class         => SafetyCheckPolicy::class,
        SafetyCheckResponse::class => SafetyCheckNotifyReplyPolicy::class,
        Company::class             => CompanyPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Only supper-admin allowed
        Gate::define('system-admin', function ($user) {
            return $user->isSupperAdmin();
        });
    }
}
