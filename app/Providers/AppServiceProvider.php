<?php

namespace App\Providers;

use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use App\Repositories\Transaction\DBTransaction;
use Core\Application\Interfaces\DBTransactionInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryRepositoryEloquent::class
        );
        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreRepositoryEloquent::class
        );

        /**
         * Database Transaction
         */
        $this->app->bind(
            DBTransactionInterface::class,
            DBTransaction::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
