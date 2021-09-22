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

class Tier {
    /**
     * @var $tier is the name of the format being played.
     */
    private $tier;

    /**
     * Tier constructor.
     * @param $tier
     */
    public function __construct($tier) {
        $this->tier = $tier;
    }

    /**
     * @return is $tier.
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @param $tier
     */
    public function setTier($tier)
    {
        $this->tier= $tier;
    }
}