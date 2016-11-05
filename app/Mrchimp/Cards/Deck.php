<?php
/**
 * It's a deck of cards. This is half-finished.
 */

namespace Mrchimp\Cards;

/**
 * It's a deck of cards.
 */
class Deck
{
  /**
   * The cards
   * @var array
   */
  private $deck = array();

  /**
   * Card suits
   * @var array
   */
  private $suits = array();

  /**
   * Card ranks A-K
   * @var array
   */
  private $ranks = array();

  /**
   * Class constructor
   */
  function __construct() {
    $this->deck  = array();
    $this->suits = array('♠','♥','♦','♣');
    $this->ranks = array('A','2','3','4','5','6','7','8','9','10','J','Q','K');
    $this->reset();
  }

  /**
   * Return all cards to the pack
   * @return [type] [description]
   */
  function reset() {
    $this->deck = array();

    foreach ($this->suits as $suit) {
      for ($rank = 1; $rank <= 13; $rank++) {

        $this->deck[] = new Card($suit, $this->ranks[$rank - 1]);
        // switch ($rank) {
        //   case 1:
        //     $this->deck[] = $suit.'A';
        //     break;
        //   case 11:
        //     $this->deck[] = $suit.'J';
        //     break;
        //   case 12:
        //     $this->deck[] = $suit.'Q';
        //     break;
        //   case 13:
        //     $this->deck[] = $suit.'K';
        //     break;
        //   default;
        //     $this->deck[] = $suit.$rank;
        // }
      }
    }
  }

  /**
   * Shuffle the order of cards in the deck
   * @return null
   */
  function shuffle() {
    shuffle($this->deck);
  }

  /**
   * Extract cards from the deck
   * @param  integer $number Number of cards to deal
   * @param  string  $origin
   * @return object
   */
  function deal($number = 1, $origin = 'top') {
    if (empty($this->deck)) {
      return false;
    }

    $chosen_cards = array();

    for ($x = 0; $x < $number; $x++) {
      $rand = rand(0,count($this->deck) - 1);
      $chosen_cards[] = $this->deck[$rand];
      unset($this->deck[$rand]);
      $this->deck = array_values($this->deck);
      if (empty($this->deck)) {
        return $chosen_cards;
      }
    }

    return $chosen_cards;
  }

  /**
   * Put a card to the top of the deck.
   */
  function push() {

  }
}
