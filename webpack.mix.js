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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
    //.copy('node_modules/dropzone/dist/dropzone.js', 'public/js/dropzone.js')
    // .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')   //no need
    .copy('resources/assets/images/line_vert.gif', 'public/images/line_vert.gif')
    .copy('resources/assets/images/send.svg', 'public/images/send.svg')
    .copy('resources/assets/images/sent.svg', 'public/images/sent.svg')
    .copy('resources/assets/images/favicon.ico', 'public/favicon.ico');