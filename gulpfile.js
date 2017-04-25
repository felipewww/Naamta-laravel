var elixir = require('laravel-elixir');

// elixir(function(mix) {
//     mix.sass([
//         './resources/assets/sass/app.scss'
//     ], 'public/css/app.css');
// });

elixir(function(mix) {
    mix.sass([
        './resources/assets/sass/naamta.scss'
    ], 'public/css/naamta.css');
});