<?php

require __DIR__.'/../bootstrap/autoload.php';

use GetOptionKit\OptionCollection;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionParser;

var_dump($argv);

$options = new OptionCollection;
// $options->add("v|verbose")->isa("number")->incremental();// Setting isa("number") will throw an InvalidOptionValue exception "Invalid value for -v, --verbose. Requires a type Number."
$options->add("v|verbose")->incremental();// This works but only sets the value to true even if specified multiple times
$options->add("foo:", "foo");

// $parser = new ContinuousOptionParser($options);// Replace ContinuousOptionParser with OptionParser and everything works as expected
$parser = new OptionParser($options);// Replace ContinuousOptionParser with OptionParser and everything works as expected
$result = $parser->parse($argv);

var_dump($result);
echo 'whut';
// var_dump($result->get('foo'));
