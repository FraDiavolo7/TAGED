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

class Team 
{
    /**
     * @var $Player is p1, p2, p3, or p4.
     */
    private $Player;

    /**
     * @var $Size is the number of Pokémon your opponent starts with. In games without ReplayParser Preview, you don't know which Pokémon your opponent has, but you at least know how many there are.
     */
    private $Size;
    
    /**
     * @var $Pokemons is the array of Pokemons, index by position
     */
    private $Pokemons;

    /**
     * TeamSize constructor.
     * @param int $Player The player position of the Team 
     * @param int $Size The Quantity of pokemons in the Team 
     */
    public function __construct ( $Player, $Size ) 
    {
        $this->Player = $Player;
        $this->Size = $Size;
    }
    
    /**
     *  @brief Creates a TeamSize from an array of data arrange as :
     *  Array [1] => Player position
     *  Array [2] => Quantity of pokemon in the team
     *  @param array $Array The array to use for filling the Palyer
     *  @return A new TeamSize object
     */
    public static function create ( $Array )
    {
        $Player = Arrays::getOrCrash ( $Array, 1, 'Team not affected to as player' );
        $Size = Arrays::getOrCrash ( $Array, 2, 'Team quantity invalid' );
        
        return new Team ( $Player, $Size );
    }

    
    public function __toString ( )
    {
        $Result = 'Team ' . $this->Player . ' has ' . $this->Size . ' pokemon (';
        $Sep = '';
        foreach ( $this->Pokemons as $Pokemon )
        {
            $Result .= $Sep . $Pokemon;
            $Sep = ', ';
        }
        $Result .= ')';
        return $Result;
    }
    
    public function switch ( $Pokemon )
    {
        if ( ! isset ( $this->Pokemons [ $Pokemon ] ) )
        {
            $this->addPokemon ( $Pokemon );
        }
    }
    
    /**
     * @return The player position of the Team.
     */
    public function getPlayer ()
    {
        return $this->Player;
    }

    /**
     * @param int $Player The player position of the Team 
     */
    public function setPlayer ( $Player )
    {
        $this->Player = $Player;
    }

    /**
     * @return The Quantity of pokemons in the Team.
     */
    public function getSize ()
    {
        return $this->Size;
    }

    /**
     * @param int $Size The Quantity of pokemons in the Team 
     */
    public function setSize ( $Size )
    {
        $this->Size = $Size;
    }
    
    /**
     * Adds a Pokemon to the list
     * @param String $Pokemon The name of the pokemon
     */
    public function addPokemon ( $Pokemon )
    {
        $this->Pokemons [ $Pokemon ] = $Pokemon;
    }
}