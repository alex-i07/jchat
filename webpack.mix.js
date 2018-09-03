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
     .copy('resources/assets/images', 'public/images');


// mix.scripts(['resources/assets/js/app.js', 'resources/assets/js/dropzone.js'], 'public/js/app.js')
//     .sass('resources/assets/sass/app.scss', 'public/css')
//     .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
//     .copy('resources/assets/images', 'public/images');