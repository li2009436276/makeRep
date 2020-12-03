<?php

namespace MakeRep;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //数据库注释
        $this->app->bind('Illuminate\Support\Facades\Schema', 'Jialeo\LaravelSchemaExtend\Schema');
        //数据库编码报错
        \Schema::defaultStringLength(191);


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
