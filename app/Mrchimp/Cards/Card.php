<?php

namespace Mrchimp\Cards;

/**
 * A single playing card
 */
class Card
{

  /**
   * The card suit. Stored as UTF-8 suit character.
   * @var string
   */
  private $suit;

  /**
   * The rank of the card. A-K
   * @var string
   */
  private $rank;

  /**
   * Typeable shorthand for suits. H, D, C or S.
   * @var string
   */
  private $suit_aliases;

  /**
   * Card suits
   * @var array
   */
  private $suits = array();

  /**
   * Whether the card is visible
   * @var boolean
   */
  private $face_up = true;

  /**
   * Card ranks A-K
   * @var array
   */
  private $ranks = array();

  /**
   * Class constructor
   * @param string  $suit    Card suit. Should be H, D, C or S.
   *                         Or UTF-8 suit character if they can be bothered
   *                         to type that
   * @param string  $rank    Card rank. A,2,3...10,J,Q,K.
   * @param boolean $face_up If false, cards value will not be visible.
   */
  function __construct($suit, $rank, $face_up = true) {
    $suit_aliases = array(
      'H' => '♥',
      'D' => '♦',
      'C' => '♣',
      'S' => '♠',
    );

    $this->face_up = !!$face_up;
    $this->suits = array('♠','♥','♦','♣');
    $this->ranks = array('A','2','3','4','5','6','7','8','9','10','J','Q','K');

    if (isset($suit_aliases[$suit])) {
      $suit = $suit_aliases[$suit];
    }

    switch ($rank) {
      case 1:
        $rank = 'A';
        break;
      case 11:
        $rank = 'J';
        break;
      case 12:
        $rank = 'Q';
        break;
      case 13:
        $rank = 'K';
        break;
      default;
        break;
    }

    if (!in_array($rank, $this->ranks)) {
      throw new Exception('Invalid rank.');
    }

    if (!in_array($suit, $this->suits)) {
      throw new Exception('Invalid suit.');
    }

    $this->suit = $suit;
    $this->rank = $rank;
  }

  /**
   * Get the card rank. Returns rank as string: A,2,3...10,J,Q,K.
   * If card is face down a space is returned.
   * @return string
   */
  function getRank() {
    return ($this->face_up ? $this->rank : ' ');
  }

  /**
   * Get the card suit. Returns rank as UTF-8 suit character.
   * If card is face down a space is returned.
   * @return string
   */
  function getSuit() {
    return ($this->face_up ? $this->suit : ' ');
  }

  /**
   * Get the color of the card as a string.
   * @return string 'black' or 'red'
   */
  function getColor() {
    return ($this->suit === '♣' || $this->suit === '♠' ? 'red' : 'black');
  }
}
