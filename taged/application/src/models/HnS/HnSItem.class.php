<?php

class HnsItem 
{
    const TABLE = 'equip';
    const ID    = 'id_equip';
    const NOM   = 'nomequip';
    const PLACE = 'place';

    const TABLE_PORTE = 'porte';
    const ID_PORTE    = 'id_porte';
    const COTE        = 'cote';
    const COTE_LEFT   = 'Left';
    const COTE_RIGHT  = 'Right';

    private $Id;
    private $IdPorte;
    private $Position;
    private $Image;
    private $Name;
    private $Affix;
    private $Place;
    private $Cote;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Position = '', $Image = '', $Name = '', $Affix = array () ) 
    {
        $this->setPosition ( $Position );
        $this->Image = $Image;
        $this->setName ( $Name );
        $this->Affix = $Affix;
    }

    public function __toString ( )
    {
        $Result = 'Item ' . $this->Name . ' on ' . $this->Place . ' ' . $this->Cote;
        $Sep = ' with ';

        foreach ( $this->Affix as $Affix )
        {
            $Result .= $Sep . $Affix;
            $Sep = ', ';
        }
        
        return $Result;
    }

    public function setPosition ( $NewValue ) 
    { 
        $this->Position = $NewValue; 
        if ( Strings::compareSameSize ( 'left', $NewValue ) )
        {
            $this->Cote = self::COTE_LEFT;
            $this->Place = str_replace ( 'left', '', $NewValue );
        }
        elseif ( Strings::compareSameSize ( 'right', $NewValue ) )
        {
            $this->Cote = self::COTE_RIGHT;
            $this->Place = str_replace ( 'right', '', $NewValue );
        }
        else
        {
            $this->Place = $NewValue;
            $this->Cote = '';
        }
    }
    public function setImage    ( $NewValue ) { $this->Image    = $NewValue; }
    public function setName     ( $NewValue ) { $this->Name     = TagedDBHnS::escape4HTML ( $NewValue ); }
    public function setAffix    ( $NewValue ) { $this->Affix    = $NewValue; }
    public function addAffix    ( $NewValue ) { $this->Affix [] = $NewValue; }
    
    public function getPosition ( ) { return $this->Position; }
    public function getImage    ( ) { return $this->Image   ; }
    public function getName     ( ) { return $this->Name    ; }
    public function getAffix    ( ) { return $this->Affix   ; }
    
    public function getId  ( )
    {
        if ( -1 == $this->Id ) $this->fetchId ();
        return $this->Id ;
    }

    private function fetchId ()
    {
        $this->Id = -1;

        TagedDBHnS::execute ( "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Name ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->Id = $Results [0] [ self::ID ];
        }
    }

    private function porte ( $HeroId )
    {
        Log::fct_enter ( __METHOD__ );

        TagedDBHnS::execute ( "INSERT INTO " . self::TABLE_PORTE . " (" . 
            self::ID . ", " . 
            Hero::ID . ", " . 
            self::COTE . ") VALUES (" .
            ""  . $this->Id    . ", " .
            ""  . $HeroId      . ", " .
            "'" . $this->Cote  . "' " . 
            ");" );

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
            TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::PLACE . ") VALUES ('" . $this->Name . "', '" . $this->Place . "');" );

            $this->fetchId ( );
        }
        
        $this->porte ( $HeroId );

        Log::fct_exit ( __METHOD__ );
    }

    
}
