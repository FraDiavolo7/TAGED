<?php

class HnSPlayer 
{

    const TABLE  = 'joueur';
    const ID     = 'id_joueur';
    const NOM    = 'nom';
    const TAG    = 'tag';
    const CLAN   = 'clan';
    
    private $Id;
    private $Username;
    private $Tag;
    private $Clan;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Username = '', $Tag = '', $Clan = '' ) 
    {
        $this->Username = $Username;
        $this->Tag = $Tag;
        $this->Clan = $Clan;
    }

    public function __toString ( )
    {
        return ( '' != $this->Clan ? '[' . $this->Clan . '] ' : '' ) . $this->Username . ' ' . $this->Tag;
    }

    public function setId       ( $NewValue ) { $this->Id       = $NewValue; }
    public function setUsername ( $NewValue ) { $this->Username = $NewValue; }
    public function setTag      ( $NewValue ) { $this->Tag      = $NewValue; }
    public function setClan     ( $NewValue ) { $this->Clan     = $NewValue; }
    
    public function getUsername ( ) { return $this->Username; }
    public function getTag      ( ) { return $this->Tag     ; }
    public function getClan     ( ) { return $this->Clan    ; }

    public function getId  ( )
    {
        if ( -1 == $this->Id ) $this->fetchId ();
        return $this->Id ;
    }

    private function fetchId ()
    {
        $this->Id = -1;

        TagedDBHnS::execute ( "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Username ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->Id = $Results [0] [ self::ID ];
        }
    }

    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        // #1 vérifie si un Utilisateur existe pour ce nom
        $this->fetchId ( );

        if ( -1 == $this->Id )
        {
            // #2 Si non, ajoute entrée Utilisateur
            TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::TAG . ", " . self::CLAN . ") VALUES ('" . $this->Username . "', '" . $this->Tag . "', '" . $this->Clan . "');" );
            $this->fetchId ();
        }

        Log::fct_exit ( __METHOD__ );
    }

    
}
