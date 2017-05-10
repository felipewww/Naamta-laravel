<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Shortcode::register('b', BoldShortcode::class);
        Shortcode::register('i', ItalicShortcode::class);
    }
}
