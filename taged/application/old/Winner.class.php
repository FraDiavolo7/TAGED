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

class Winner 
{
    /**
     * @var $User is the User that has won the battle.
     */
    private $User;

    /**
     * Winner constructor.
     * @param $User
     */
    public function __construct ( $User ) 
    {
        $this->User = $User;
    }
    
    /**
     *  @brief Creates a Winner from an array of data arrange as :
     *  Array [1] => User
     *  @param array $Array The array to use for filling the Winner
     *  @return A new Winner object
     */
    public static function create ( $Array )
    {
        $User = Arrays::getOrCrash ( $Array, 1, 'User not filled' );
        
        return new Winner ( $User );
    }
    
    public function __toString ( )
    {
        return 'Winner : ' . $this->User;
    }
    
    /**
     * @return is $User.
     */
    public function getUser ()
    {
        return $this->User;
    }

    /**
     * @param $User.
     */
    public function setUser ( $User )
    {
        $this->User = $User;
    }
}