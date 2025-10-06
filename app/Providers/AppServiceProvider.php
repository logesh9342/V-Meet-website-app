<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        // Provide a list of contacts (other registered users) to all views for the sidebar
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $contacts = User::query()
                    ->where('id', '!=', Auth::id())
                    ->orderBy('name')
                    ->limit(20)
                    ->get(['id', 'name']);
                $view->with('sidebarContacts', $contacts);
            }
        });
    }
}
