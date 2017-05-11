<?php

namespace App\Shortcodes;

class ItalicShortcode {

    public function register($shortcode, $content, $compiler, $name)
    {
        return sprintf('<i class="%s">%s</i>', $shortcode->class, $content);
    }
}