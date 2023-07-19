<?php

/**
 * Collection Player Data
 * @package TAGED\Models\Collection
 */
class CollPlayer 
{
    const TABLE  = 'utilisateur';
    const NOM    = 'nom';
    const AVATAR = 'avatar';
    const RATING = 'elo';
    const NUMERO = 'numero';
    const VICTOIRE = 'victoire';
    
    /**
     * @var $Player is p1 or p2 most of the time, may also be p3 or p4 in 4 players battles.
     */
    private $Player;

    /**
     * @var $Username is the username.
     */
    private $Username;

    /**
     * @var $Avatar is the player's avatar identifier (usually a number, but other values can be used for custom avatars).
     */
    private $Avatar;

    /**
     * @var $Rating is the player's Elo rating in the format they're playing. This will only be displayed in rated battles and when the player is first introduced otherwise it's blank.
     */
    private $Rating;

    /**
     * @brief Player constructor.
     * @param int $Player The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Player, $Username, $Avatar, $Rating) 
    {
        $this->Player = $Player;
        $this->setUsername ( $Username );
        $this->Avatar = $Avatar;
        $this->Rating = $Rating;
    }
    
    public function __toString ( )
    {
        return 'Player ' . $this->Player . ' is ' . $this->Username . ' ' . $this->Rating . 'ELO';
    }

    /**
     * @return The position of the player
     */
    public function getPlayer ()
    {
        return $this->Player;
    }

    /**
     * @param int $Player The position of the player
     */
    public function setPlayer ( $Player )
    {
        $this->Player = $Player;
    }

    /**
     * @return The name of the user playing
     */
    public function getUsername ()
    {
        return $this->Username;
    }

    /**
     * @param string $Username The name of the user playing
     */
    public function setUsername ( $Username )
    {
        $this->Username = TagedDBColl::escape4HTML ( $Username );
    }

    /**
     * @return The avatar of the user
     */
    public function getAvatar ()
    {
        return $this->Avatar;
    }

    /**
     * @param int $Avatar The avatar of the user
     */
    public function setAvatar ( $Avatar )
    {
        $this->Avatar = $Avatar;
    }

    /**
     * @return The rating of the user.
     */
    public function getRating ()
    {
        return $this->Rating;
    }

    /**
     * @param int $Rating The rating of the user
     */
    public function setRating ( $Rating )
    {
        if ( 0 == $Rating )
        {
            TagedDBColl::execute ( "SELECT " . self::RATING . " FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Username ."'" );
            $Results = TagedDBColl::getResults ( );
            
            $Rating = Arrays::getIfSet ( $Results, self::RATING, 1000 );
        }
        $this->Rating = $Rating;
    }
    
    public function save ()
    {
        Log::fct_enter ( __METHOD__ );
        
        // #1 v�rifie si un Utilisateur existe pour ce nom
        TagedDBColl::execute ( "SELECT *  FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Username ."'" );
        $Results = TagedDBColl::getResults ( );
        
        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entr�e Utilisateur
            TagedDBColl::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::AVATAR . ", " . self::RATING . ") VALUES ('" . $this->Username . "', '" . $this->Avatar . "', " . $this->Rating . ");" );
        }
        else 
        {
            $Elo = Arrays::getIfSet ( $Results, self::RATING,  0 );
            
            if ( $this->Rating != $Elo )
            {
                TagedDBColl::execute ( "UPDATE " . self::TABLE . " SET " . self::RATING . " = " . $this->Rating . " WHERE " . self::NOM . " = '" . $this->Username ."'" );
            }
        }
        
        Log::fct_exit ( __METHOD__ );
    }
    
}
