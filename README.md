# Chimpcom #

[Chimpcom](http://deviouschimp.co.uk/cmd) is a command line website built on the [Lumen framework](http://lumen.laravel.com/).

Chimpcom was originally made in 2009 but this repository is a complete re-write started on 29th August 2015.

# Usage #

    command param1 param2 -f --flag @name #tag

Chimpcom syntax is pretty basic. The command name goes first. Any word after that is either a parameter, a flag, a name or a tag.

## Parameters ##

The parameters are read in order, regardless of position and there are currently no named parameters, so the following are equivalent:

    command param1 param2 --flag
    command param1 --flag #tag param2

## Flags ##

Flags are either set or not. Flags can be short `-f` or long `--flag`.

## Names ##

You can refer to other Chimpcom users in some commands with Twitter-like `@name syntax`.

## Tags ##

You can use tags in some commands with `#hashtag` syntax.

# Commands #

[See wiki for command list](comingsoon)

# Development #

## Installation ##

1. Set up a PHP web server
2. Get code
3. Point your server at `/public`
4. `composer install`
5. `php artisan migrate`

## Dev tools ##

You'll need [Node](http://nodejs.org/), [Grunt](http://gruntjs.com/) and [Bower](http://bower.io/).

1. `npm install` Get dev tools
2. `bower install` Get frontend dependencies
3. `grunt` Compile everything
4. `grunt watch-all` Watch for changes to code and recompile

## Docs ##

To generate Chimpcom docs, install [phpdoc](http://www.phpdoc.org) then run:

    makedocs.sh


You'll want the [Lumen docs](lumen.laravel.com/docs), too.