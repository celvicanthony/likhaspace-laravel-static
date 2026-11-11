<?php

namespace App\Providers;

use App\Models\ArtistCommissionPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

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
        view()->composer('layouts.app', function (View $view): void {
            $user = Auth::user();
            $pendingCommissionSettlements = $user?->role === 'admin'
                && Schema::hasTable('artist_commission_payments')
                ? ArtistCommissionPayment::where('status', 'submitted')->count()
                : 0;

            $view->with('pendingCommissionSettlements', $pendingCommissionSettlements);
        });
    }
}
