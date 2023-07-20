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

class Rated 
{
    /**
     * @var $Rated is true if the game will affect the player's ladder rating (Elo score), false otherwise.
     */
    private $Rated;

    /**
     * @var $Message Will be sent if the game is official in some other way, such as being a tournament game. Does not actually mean the game is Rated.
     */
    private $Message;

    /**
     * Rated constructor.
     * @param bool $Rated If the game is announced as Rated
     * @param string $Message The message officialising the game
     */
    public function __construct ( $Rated, $Message = '' ) 
    {
        $this->Rated = $Rated;
        $this->Message = $Message;
    }

    /**
     *  @brief Creates a Rated from an array of data arrange as :
     *  Array [1] => Rated
     *  Array [2] => Message
     *  @note If Rated is filled but not Message, it means the game is rated. If Message is filled, the game is official, but not necessarily rated
     *  @param array $Array The array to use for filling the Rated
     *  @return A new Rated object
     */
    public static function create ( $Array )
    {
        $Rated   = Arrays::getOrCrash ( $Array, 1, 'Rated not filled' );
        $Message = Arrays::getIfSet   ( $Array, 2, '' );
        
        return new Rated ( ( $Rated == 'rated' ), $Message );
    }
    
    public function __toString ( )
    {
        return ( $this->Rated ? '' : 'not ' ) . 'Rated ' . $this->Message;
    }
    
    /**
     * @return bool If the game is announced as Rated
     */
    public function getRated ()
    {
        return $this->Rated;
    }

    /**
     * @param bool $Rated If the game is announced as Rated
     */
    public function setRated ( $Rated )
    {
        $this->Rated = $Rated;
    }

    /**
     * @return string The message officialising the game
     */
    public function getMessage ()
    {
        return $this->Message;
    }

    /**
     * @param string $Message The message officialising the game
     */
    public function setMessage ( $Message )
    {
        $this->Message = $Message;
    }
}