<?php

namespace App\Providers;

use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Auth\Repositories\Contracts\AuthRepositoryInterface;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Modules\AuditLog\Repositories\AuditLogRepository;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;
use App\Modules\Payment\Repositories\PaymentRepository;
use App\Modules\Sale\Repositories\Contracts\SaleRepositoryInterface;
use App\Modules\Sale\Repositories\SaleRepository;
use App\Modules\SaleDetail\Repositories\Contracts\SaleDetailRepositoryInterface;
use App\Modules\SaleDetail\Repositories\SaleDetailRepository;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SaleRepositoryInterface::class, SaleRepository::class);
        $this->app->bind(SaleDetailRepositoryInterface::class, SaleDetailRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
