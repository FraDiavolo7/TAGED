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

class TeamSize {
    /**
     * @var $plauer is p1, p2, p3, or p4.
     */
    private $player;

    /**
     * @var $number is the number of Pokémon your opponent starts with. In games without ReplayParser Preview, you don't know which Pokémon your opponent has, but you at least know how many there are.
     */
    private $number;

    /**
     * TeamSize constructor.
     * @param $player
     * @param $number
     */
    public function __construct($player, $number) {
        $this->player = $player;
        $this->number = $number;
    }

    /**
     * @return is $player.
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
     * @return is $number.
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }
}