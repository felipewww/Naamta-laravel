var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.sass([
        './resources/assets/sass/app.scss'
    ], 'public/css/app.css');
});