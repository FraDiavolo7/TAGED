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

class GameType {
    /**
     * @var $gameType is singles, doubles, triples, multi, or free-for-all.
     */
    private $gameType;

    /**
     * GameType constructor.
     * @param $gameType
     */
    public function __construct($gameType) {
        $this->gameType = $gameType;
    }

    /**
     * @return is $gameType.
     */
    public function getGameType()
    {
        return $this->gameType;
    }

    /**
     * @param $gameType
     */
    public function setGameType($gameType)
    {
        $this->gameType= $gameType;
    }
}