<?php
namespace App\Shortcodes;

use App\Models\User;

class UserNameShortcode {
    public function register($shortcode, $content, $compiler, $name)
    {
        $user = User::find($shortcode->user_id);
        return sprintf('<span class="user%s">%s</span>', $shortcode->user_id, $user->name);
    }
}