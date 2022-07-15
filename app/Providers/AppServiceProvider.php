<?php

namespace App\Providers;

use App\Models\Avatar;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        Paginator::useBootstrap();

        view()->composer('*', function ($view) {
            if(Auth::check()){
                $myavatar = Avatar::where('user_id','=',Auth::user()->id)->latest()->first();
                
                View::share('myavatar', $myavatar);
            }
        });
    }
}
