const mix = require('laravel-mix');

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

mix
  .js(['resources/js/main.js'], 'public/js')
  .less('resources/less/main.less', 'public/css')
  .copy('resources/images', 'public/img')
  .version(['public/js/main.js', 'public/css/main.css']);