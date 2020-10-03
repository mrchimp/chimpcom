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
     *
     * @var array
     */
    private $deck = [];

    /**
     * Card suits
     * @var array
     */
    private $suits = [];

    /**
     * Card ranks A-K
     * @var array
     */
    private $ranks = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->deck  = [];
        $this->suits = ['♠','♥','♦','♣'];
        $this->ranks = ['A','2','3','4','5','6','7','8','9','10','J','Q','K'];
        $this->reset();
    }

    /**
     * Return all cards to the pack
     */
    public function reset(): void
    {
        $this->deck = [];

        foreach ($this->suits as $suit) {
            for ($rank = 1; $rank <= 13; $rank++) {
                $this->deck[] = new Card($suit, $this->ranks[$rank - 1]);
            }
        }
    }

    /**
     * Shuffle the order of cards in the deck
     */
    public function shuffle(): void
    {
        shuffle($this->deck);
    }

    /**
     * Extract cards from the deck
     */
    public function deal(int $number = 1, string $origin = 'top'): array
    {
        if (empty($this->deck)) {
            return null;
        }

        $chosen_cards = array();

        for ($x = 0; $x < $number; $x++) {
            $rand = rand(0, count($this->deck) - 1);
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
    public function push()
    {
        //
    }
}
