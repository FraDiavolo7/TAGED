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

class TeamSize 
{
    /**
     * @var $Player is p1, p2, p3, or p4.
     */
    private $Player;

    /**
     * @var $Number is the number of Pokémon your opponent starts with. In games without ReplayParser Preview, you don't know which Pokémon your opponent has, but you at least know how many there are.
     */
    private $Number;

    /**
     * TeamSize constructor.
     * @param [in] $Player The player position of the Team 
     * @param [in] $Number The Quantity of pokemons in the Team 
     */
    public function __construct ( $Player, $Number ) 
    {
        $this->Player = $Player;
        $this->Number = $Number;
    }
    
    /**
     *  @brief Creates a TeamSize from an array of data arrange as :
     *  Array [1] => Player position
     *  Array [2] => Quantity of pokemon in the team
     *  @param [in] $Array The array to use for filling the Palyer
     *  @return A new TeamSize object
     */
    public static function create ( $Array )
    {
        $Player = Arrays::getIfSet ( $Array, 1, 'p1');
        $Number = Arrays::getIfSet ( $Array, 2, '0' );
        
        return new TeamSize ( $Player, $Number );
    }

    /**
     * @return The player position of the Team.
     */
    public function getPlayer ()
    {
        return $this->Player;
    }

    /**
     * @param [in] $Player The player position of the Team 
     */
    public function setPlayer ( $Player )
    {
        $this->Player = $Player;
    }

    /**
     * @return The Quantity of pokemons in the Team.
     */
    public function getNumber ()
    {
        return $this->Number;
    }

    /**
     * @param [in] $Number The Quantity of pokemons in the Team 
     */
    public function setNumber ( $Number )
    {
        $this->Number = $Number;
    }
}