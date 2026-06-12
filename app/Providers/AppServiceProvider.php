<?php

namespace App\Providers;

use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\PaymentMethodRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\ExpenseRepositoryInterface;
use App\Repositories\PaymentMethodRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
