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

class Rule {
    /**
     * @var $rule is discribing rule.
     */
    private $rule;

    /**
     * Rule constructor.
     * @param $rule
     */
    public function __construct($rule) {
        $this->rule = $rule;
    }

    /**
     * @return is $rule.
     */
    public function getTier()
    {
        return $this->rule;
    }

    /**
     * @param $rule
     */
    public function setTier($rule)
    {
        $this->rule= $rule;
    }
}