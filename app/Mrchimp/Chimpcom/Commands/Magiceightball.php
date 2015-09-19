<?php 
/**
 * Get an answers
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Get an answers
 */
class Magiceightball extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if ($this->input->isFlagSet(['-f', '--force'])) {
      $answers = [
        'It is your destiny.',
        'It is pointless to resist, my son.',
        'I\'ve got a bad feeling about this.',
        'Difficult to see. Always in motion is the future.',
        'Your feelings betray you.',
        'Search your feelings, you know it to be true.',
        'Noooooooooooooooo!'
      ];
    } else {
      $answers = [
        "Don't count on it.",
        "It is decidedly so.",
        "My reply is no.",
        "My sources say no.",
        "Outlook not so good.",
        "Very doubtful.",
        "Reply hazy, try again.",
        "Concentrate and ask again.",
        "Ask again later.",
        "Better not tell you now.",
        "Cannot predict now.",
        "As I see it, Yes.",
        "It is certain.",
        "Most likely.",
        "Outlook good.",
        "Signs point to yes.",
        "Without a doubt.",
        "Yes.",
        "Yes definitely.",
        "You may rely on it.",
        "Probably.",
        "Maybe.",
        "Fuck knows.",
        "Dunno.",
        "Negative.",
        "Indeed.",
        "I doubt it.",
        "Meh.",
        "Doubtful.",
        "I say no.",
        "I say yes."
      ];
    }

    $this->response->say($answers[mt_rand(0, (count($answers) - 1))]);
  }

}