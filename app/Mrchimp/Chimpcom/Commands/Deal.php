<?php 
/**
 * Deals a card from a pack
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;
use Mrchimp\DeckOfCards;

/**
 * Deals a card from a pack
 */
class Deal extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    $count = ($this->input->get(1) ? $this->input->get(1) : 6);

    $deck = new DeckOfCards();
    $hand = $deck->deal($count);

    // Output ten per line
    $x = 0;
    foreach ($hand as $card) {
      if ($x % 10 == 0 && $x > 0) {
        $this->response->say('<br>');
      }
      $this->response->say(Format::style($card->getSuit() . $card->getRank(), 'card ' . $card->getColor()));
      $x++;
    }
  }

}