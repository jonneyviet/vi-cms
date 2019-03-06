let mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.version();
mix.options({ processCssUrls: false,sourceMap: true });
mix.js('resources/assets/admin/js/app.js', 'public/admin-vi/js').extract(['vue']).version();
mix.sass('resources/assets/admin/sass/app.scss', 'public/admin-vi/css').sourceMaps();;
