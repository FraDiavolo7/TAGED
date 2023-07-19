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
 * @deprecated
 * @package Deprecated
 */

class Turn {
    private $turn;
    private $p1Move;
    private $p2Move;

    public function __construct($turn) {
        $this->turn = $turn;
    }

    /**
     * @return mixed
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * @param mixed $turn
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;
    }

    /**
     * @return mixed
     */
    public function getP1Move()
    {
        return $this->p1Move;
    }

    /**
     * @param mixed $p1Move
     */
    public function setP1Move($p1Move)
    {
        $this->p1Move = $p1Move;
    }

    /**
     * @return mixed
     */
    public function getP2Move()
    {
        return $this->p2Move;
    }

    /**
     * @param mixed $p2Move
     */
    public function setP2Move($p2Move)
    {
        $this->p2Move = $p2Move;
    }
}