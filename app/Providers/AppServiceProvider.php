<?php

namespace App\Providers;

use App\Services\Comment\CommentService;
use App\Services\Comment\ICommentService;
use App\Services\Post\IPostService;
use App\Services\Post\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IPostService::class, PostService::class);
        $this->app->singleton(ICommentService::class, CommentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
