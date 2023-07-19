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

class GameType 
{
    /**
     * @var $GameType is singles, doubles, triples, multi, or free-for-all.
     */
    private $GameType;

    /**
     * GameType constructor.
     * @param string $GameType The type of game
     */
    public function __construct ( $GameType ) 
    {
        $this->GameType = $GameType;
    }

    public function __toString ()
    {
        return 'Game Type ' . $this->GameType; 
    }
    
    /**
     *  @brief Creates a Game Type from an array of data arrange as :
     *  Array [1] => Game Type
     *  @param array $Array The array to use for filling the Game Type
     *  @return A new Game Type object
     */
    public static function create ( $Array )
    {
        $GameType = Arrays::getOrCrash ( $Array, 1, 'Invalid Game Type' );
        
        return new GameType ( $GameType );
    }
    
    /**
     * @return string The type of game
     */
    public function getGameType ()
    {
        return $this->GameType;
    }

    /**
     * @param string $GameType The type of game
     */
    public function setGameType ( $GameType )
    {
        $this->GameType= $GameType;
    }
}