<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        Builder::macro('advanced', function($conditions = []) {
            return (new ConditionsBuilder($this))->attach($conditions);
        });

        Request::macro('conditions', function($wheres = []) {
            return (new ConditionsGenerator($this, $wheres))->getRequestConditions();
        });
    }
}
