<?php

namespace App\Providers;

use App\Models\LoyaltyMember;
use App\Models\User;
use App\Observers\LoyaltyMemberObserver;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();
        LoyaltyMember::observe(LoyaltyMemberObserver::class);
    }
}
