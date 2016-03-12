var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('global.scss', './public/css/global_vendor.css')
        .sass('main.scss', './public/css/outer.css')
        .sass('inner.scss', './public/css/inner.css');

    mix.scripts(['jquery-2.1.4.js', 'bootstrap.min.js'], './public/js/global_vendor.js')
        .scripts(['jquery.preload.min.js', 'socket.io-1.3.5.js'], './public/js/inner_vendor.js')
        .browserify(['inner/inner.js'], './public/js/inner.js');

    mix.version(['css/*', 'js/*']);
});
