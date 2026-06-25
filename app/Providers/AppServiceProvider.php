<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Complaint;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Kirim $pending ke semua view yang pakai layout admin
        View::composer('layouts.admin', function ($view) {
            $view->with('pending', Complaint::where('status', 'pending')->count());
        });
    }
}