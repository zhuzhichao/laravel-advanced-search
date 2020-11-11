<?php
namespace Illuminate\Database\Eloquent;
if (false) {
    /**
     * @method static Builder advanced($conditions = []) Laravel advanced main function
     */
    class Model{}
}

namespace Illuminate\Database\Eloquent;
if (false) {
    /**
     * @method Builder advanced($conditions = []) Laravel advanced main function
     */
    class Builder{}
}

namespace Zhuzhichao\LaravelAdvancedSearch;

use Illuminate\Database\Eloquent\Builder;
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
            return (new ConditionsBuilder($this))->attach((new ConditionsGenerator($conditions))->getRequestConditions());
        });
    }
}
