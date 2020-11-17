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
  .ts(['resources/js/main.ts'], 'public/js')
  .less('resources/less/cmd/main.less', 'public/css')
  .less('resources/less/blog/main.less', 'public/css/blog.css')
  .copy('resources/images', 'public/img')
  .version(['public/js/main.js', 'public/css/main.css']);
