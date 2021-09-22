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

class Move {
    private $player;
    private $pokemon;
    private $move;
    private $type;
    private $targetPlayer;
    private $targetPokemon;
    private $power;

    private $effect;

    public function __construct($player, $pokemon, $move, $effect) {
        $this->player = $player;
        $this->pokemon = $pokemon;
        $this->move = $move;
        /*
        $this->type = $type;
        $this->targetPlayer = $targetPlayer;
        $this->targetPokemon = $targetPokemon;
        $this->power = $power;
        */
        $this->effect = $effect;
    }
}