<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pengaturan\WebSetting;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        // View composer untuk inject $web ke semua view
        View::composer('*', function ($view) {
            $web = WebSetting::find(1); // atau where('id', 1)->first()
            $view->with('web', $web);
        });
    }
}
