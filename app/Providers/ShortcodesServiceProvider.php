<?php

namespace App\Providers;
use App\Shortcodes\BoldShortcode;
use App\Shortcodes\ItalicShortcode;
use App\Shortcodes\UserNameShortcode;
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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \Shortcode::register('b', BoldShortcode::class);
        \Shortcode::register('i', ItalicShortcode::class);
        \Shortcode::register('UserName', UserNameShortcode::class);
    }
}
