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
    mix.sass('global.scss', './public/css/global.css').sass('main.scss', './public/css/main.css').sass('surf.scss', './public/css/surf.css');

    mix.scripts(['jquery-2.1.4.js', 'bootstrap.min.js'], './public/js/global.js').scripts(['jquery.preload.min.js', 'jquery.spritely.js', 'socket.io-1.3.5.js', 'vue.min.js', 'vue-resource.min.js', 'surf.js'], './public/js/surf.js');

    mix.version(['css/*', 'js/*']);
});
