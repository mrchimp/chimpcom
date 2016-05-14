<?php
/**
 * Parser test
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use GetOptionKit\OptionCollection;

/**
 * Parser test
 */
class Parser extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process()
    {
        $this->response->title('<br>keys<br>');
        foreach ($this->input as $key => $spec) {
            // echo '<pre>';
            // var_dump($spec);
            // echo '</pre>';
            $this->response->say($key . ' - ' . $spec->value . '<br>');
        }

        $this->response->title('<br>Arguments<br>');
        foreach ($this->input->arguments as $word) {
            $this->response->say($word . '<br>');
        }
        // exit;
    }

    public function getSpecs()
    {
      $specs = new OptionCollection;

      $specs->add('f|foo:', 'option requires a value.' )
          ->isa('String');

      $specs->add('b|bar+', 'option with multiple value.' )
          ->isa('Number');

      $specs->add('ip+', 'Ip constraint' )
          ->isa('Ip');

      $specs->add('email+', 'Email address constraint' )
          ->isa('Email');

      $specs->add('z|zoo?', 'option with optional value.' )
          ->isa('Boolean');

      $specs->add('file:', 'option value should be a file.' )
          ->isa('File');

      $specs->add('v|verbose', 'verbose message.' )->isa('Number')->incremental();
      $specs->add('d|debug', 'debug message.' );
      $specs->add('long', 'long option name only.' );
      $specs->add('s', 'short option name only.' );

      return $specs;
    }
}
