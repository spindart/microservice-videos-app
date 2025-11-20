<?php

namespace App\Providers;

use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Domain\Entity\Genre;
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
