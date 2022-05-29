<?php

class HnsItem 
{
    const TABLE = 'equip';
    const NOM   = 'nomequip';
    const PLACE = 'place';

    const TABLE_PORTE = 'porte';
    const ID_PORTE    = 'id_porte';
    const COTE        = 'cote';
    const COTE_LEFT   = 'Left';
    const COTE_RIGHT  = 'Right';

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
        $this->Name = $Name;
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
    public function setName     ( $NewValue ) { $this->Name     = $NewValue; }
    public function setAffix    ( $NewValue ) { $this->Affix    = $NewValue; }
    public function addAffix    ( $NewValue ) { $this->Affix [] = $NewValue; }
    
    public function getPosition ( ) { return $this->Position; }
    public function getImage    ( ) { return $this->Image   ; }
    public function getName     ( ) { return $this->Name    ; }
    public function getAffix    ( ) { return $this->Affix   ; }
    
    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        // #1 vérifie si un Utilisateur existe pour ce nom
        TagedDBHnS::execute ( "SELECT * FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Name ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entrée Utilisateur
            TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::PLACE . ") VALUES ('" . $this->Name . "', '" . $this->Place . "');" );
        }

        Log::fct_exit ( __METHOD__ );
    }

    
}
