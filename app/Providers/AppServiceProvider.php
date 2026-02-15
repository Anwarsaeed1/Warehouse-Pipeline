<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Listeners\SendLowStockNotification;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use App\Observers\User\UserObserver;
use App\Policies\Inventory\InventoryItemPolicy;
use App\Policies\Inventory\StockPolicy;
use App\Policies\Inventory\StockTransferPolicy;
use App\Policies\Inventory\WarehousePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

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
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(Stock::class, StockPolicy::class);
        Gate::policy(StockTransfer::class, StockTransferPolicy::class);

        User::observe(UserObserver::class);

        Event::listen(LowStockDetected::class, SendLowStockNotification::class);
    }
}
