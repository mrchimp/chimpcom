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
  mix.less('main.less')
    .version('css/main.css');
});

elixir(function(mix) {
  mix.scripts([
    'querystring.js',
    '../bower_components/lodash/lodash.js',
    '../bower_components/jquery-filedrop/jquery.filedrop.js',
    '../bower_components/cmd/src/js/CmdStack.js',
    '../bower_components/cmd/src/js/Cmd.js',
    'chimpcom.js',
    'main.js'
  ], 'public/js/main.js');
});
