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

class Gen {
    /**
     * @var $gen is a number, from 1 to 7. Stadium counts as its respective gens; Let's Go counts as 7, and modded formats count as whatever gen they were based on.
     */
    private $gen;

    /**
     * Gen constructor.
     * @param $gen
     */
    public function __construct($gen) {
        $this->gen = $gen;
    }

    /**
     * @return is $gen.
     */
    public function getGen()
    {
        return $this->gameType;
    }

    /**
     * @param $gen
     */
    public function setGen($gen)
    {
        $this->gen = $gen;
    }
}