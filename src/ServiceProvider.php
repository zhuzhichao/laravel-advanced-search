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
        Builder::macro('advanced', function(Builder $builder, $conditions) {
            return (new ConditionsBuilder($builder))->attach($conditions);
        });

        Request::macro('conditions', function(Request $request, $wheres) {
            return (new ConditionsGenerator($request, $wheres))->getRequestConditions();
        });
    }
}
