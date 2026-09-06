<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use App\Models\AuditLog;
use App\Policies\AuditLogPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SaleDetailPolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(SaleDetail::class, SaleDetailPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
