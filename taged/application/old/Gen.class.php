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

class Gen 
{
    /**
     * @var $Gen is a number, from 1 to 7. Stadium counts as its respective Gens; Let's Go counts as 7, and modded formats count as whatever Gen they were based on.
     */
    private $Gen;

    /**
     * Gen constructor.
     * @param int $Gen The generation of Pokemon
     */
    public function __construct ( $Gen ) 
    {
        $this->Gen = $Gen;
    }

    public function __toString ()
    {
        return 'Gen ' . $this->Gen;
    }
    
    /**
     *  @brief Creates a Gen from an array of data arrange as :
     *  Array [1] => Gen
     *  @param array $Array The array to use for filling the Gen
     *  @return A new Gen object
     */
    public static function create ( $Array )
    {
        $Gen = Arrays::getOrCrash ( $Array, 1, 'Invalid Generation' );
        
        return new Gen ( $Gen );
    }
    
    /**
     * @return int $Gen The generation of Pokemon
     */
    public function getGen ()
    {
        return $this->Gen;
    }

    /**
     * @param int $Gen The generation of Pokemon
     */
    public function setGen ( $Gen )
    {
        $this->Gen = $Gen;
    }
}