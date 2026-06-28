<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \App\Models\Event::observe(\App\Observers\EventObserver::class);

        \Illuminate\Support\Facades\View::composer('layouts.dashboard', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $roles = [$user->role];
                
                // Add appointed roles if applicable
                if ($user->isAppointedHod()) $roles[] = 'hod';
                if ($user->isAppointedPatron()) $roles[] = 'patron';
                
                // Add Smart Ticker Insights
                $tickerService = app(\App\Services\SmartTickerService::class);
                $view->with('smartInsights', $tickerService->getLiveInsights());
            }
        });
    }
}
