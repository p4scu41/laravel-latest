<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $activityLogCls = config('activitylog.activity_model');

        $activityLogCls::saving(function ($activity) {
            $activity->properties = $activity->properties->put('ip', request()->ip());
            $activity->properties = $activity->properties->put('agent', request()->userAgent());
        });
    }
}
