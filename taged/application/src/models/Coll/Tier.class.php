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

class Tier 
{
    /**
     * @var $Tier is the name of the format being played.
     */
    private $Tier;

    /**
     * Tier constructor.
     * @param string $Tier The name of the format being played
     */
    public function __construct ( $Tier ) 
    {
        $this->Tier = $Tier;
    }

    public function __toString ()
    {
        return 'Tier ' . $this->Tier;
    }
    
    /**
     *  @brief Creates a Tier from an array of data arrange as :
     *  Array [1] => Tier
     *  @param array $Array The array to use for filling the Tier
     *  @return A new Tier object
     */
    public static function create ( $Array )
    {
        $Tier = Arrays::getOrCrash ( $Array, 1, 'Invalid Tier' );
        
        return new Tier ( $Tier );
    }
    
    /**
     * @return string The name of the format being played
     */
    public function getTier ()
    {
        return $this->Tier;
    }

    /**
     * @param string $Tier The name of the format being played
     */
    public function setTier ( $Tier )
    {
        $this->Tier = $Tier;
    }
}