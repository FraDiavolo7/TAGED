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

class Winner {
    /**
     * @var $user is the user that has won the battle.
     */
    private $user;

    /**
     * Winner constructor.
     * @param $user
     */
    public function __construct($user) {
        $this->user = $user;
    }

    /**
     * @return is $user.
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user.
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}