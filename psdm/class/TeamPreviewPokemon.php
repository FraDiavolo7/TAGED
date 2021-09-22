<?php
/*
 * Copyright © 2013 Diveen
 * All Rights Reserved.
 *
 * This software is proprietary and confidential to Diveen ReplayParser
 * and is protected by copyright law as an unpublished work.
 *
 * Unauthorized access and disclosure strictly forbidden.
 */

/**
 * @author Mickaël Martin-Nevot
 */

class TeamPreviewPokemon {

    /**
     * @var $player is the player ID.
     */
    private $player;

    /**
     * @var $details describes the pokemon.
     */
    private $details;

    /**
     * @var $item will be item if the Pokémon is holding an item, or blank if it isn't.
     */
    private $item;

    /**
     * TeamPreviewPokemon constructor.
     * @param $player
     * @param $details
     * @param $item
     */
    public function __construct($player, $details, $item)
    {
        $this->player = $player;
        $this->details = $details;
        $this->item = $item;
    }

    /**
     * @return $player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return $details
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return $item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }
}