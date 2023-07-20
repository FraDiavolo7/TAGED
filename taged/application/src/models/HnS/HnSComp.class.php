<?php

/**
 * Hack'N Slash Competence Data
 * @package TAGED\Models\HackNSlash
 */
class HnsComp 
{
    const TABLE = 'comp';
    const ID   = 'id_comp';
    const NOM  = 'nomcomp';
    const TYPE = 'typecomp';

    const TABLE_AFFECTE = 'affecte';
    const RUNE  = 'rune';
    const ORDRE = 'ordre';

    const TYPE_ACTIVE = 'active';
    const TYPE_PASSIVE = 'passive';
    
    private $Id;
    private $Skill;
    private $Order;
    private $Rune;
    private $Type;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Skill = '', $Order = '', $Type = self::TYPE_PASSIVE, $Rune = '' ) 
    {
        $this->Id = -1;
        $this->setSkill ( $Skill );
        $this->setOrder ( $Order );
        $this->setRune ( $Rune );
        $this->setType ( $Type );
    }

    public function __toString ( )
    {
        return 'Comp ' . $this->Type . ' #' . $this->Order . ' ' . $this->Skill . ( '' != $this->Rune ? ' with ' . $this->Rune : '' );
    }

    public function setSkill ( $NewValue ) { $this->Skill = TagedDBHnS::escape4HTML ( $NewValue ); }
    public function setOrder ( $NewValue ) { $this->Order = $NewValue; }
    public function setRune  ( $NewValue ) { $this->Rune  = TagedDBHnS::escape4HTML ( $NewValue ); }
    public function setType  ( $NewValue ) { $this->Type  = $NewValue; }
    
    public function getSkill ( ) { return $this->Skill; }
    public function getOrder ( ) { return $this->Order; }
    public function getRune  ( ) { return $this->Rune ; }
    public function getType  ( ) { return $this->Type ; }

    public function getId  ( ) 
    {
        if ( -1 == $this->Id ) $this->fetchId (); 
        return $this->Id ; 
    }
    
    private function fetchId ()
    {
        $this->Id = -1;

        TagedDBHnS::execute ( "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Skill ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->Id = $Results [0] [ self::ID ];
        }
    }

    private function affecte ( $HeroId )
    {
        Log::fct_enter ( __METHOD__ );

        TagedDBHnS::execute ( "INSERT INTO " . self::TABLE_AFFECTE . " (" . 
            self::ID . ", " . 
            Hero::ID . ", " . 
            self::RUNE . ", " . 
            self::ORDRE . ") VALUES (" .
            ""  . $this->Id    . ", " .
            ""  . $HeroId      . ", " .
            "'" . $this->Rune  . "', " . 
            ""  . $this->Order . ");" );

        Log::fct_exit ( __METHOD__ );
    }

    public function save ( $HeroId )
    {
        Log::fct_enter ( __METHOD__ );

        // #1 vérifie si un Utilisateur existe pour ce nom
        $this->fetchId ( );

        if ( -1 == $this->Id )
        {
            // #2 Si non, ajoute entrée Utilisateur
            TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::TYPE . ") VALUES ('" . $this->Skill . "', '" . $this->Type . "');" );
            $this->fetchId ( );
        }

        $this->affecte ( $HeroId );

        Log::fct_exit ( __METHOD__ );
    }
    
}
