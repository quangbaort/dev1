<?php

namespace App\Providers;

use App\Helpers\AppHelpers;
use App\Services\FileSystem\Storage;
use App\View\Components\Alert;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('helper', AppHelpers::class);
        $this->app->bind('fileManager', Storage::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('alert', Alert::class);
    }
}
