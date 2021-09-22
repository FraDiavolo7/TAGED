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

class Rated {
    /**
     * @var $rated is true if the game will affect the player's ladder rating (Elo score), false otherwise.
     */
    private $rated;

    /**
     * @var $message Will be sent if the game is official in some other way, such as being a tournament game. Does not actually mean the game is rated.
     */
    private $message;

    /**
     * Rated constructor.
     * @param $rated
     * @param $message
     */
    public function __construct($rated, $message) {
        $this->rated = $rated;
        $this->message = $message;
    }

    /**
     * @return is $rated
     */
    public function getRated()
    {
        return $this->rated;
    }

    /**
     * @param $rated
     */
    public function setRated($rated)
    {
        $this->rated = $rated;
    }

    /**
     * @return mixed $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}