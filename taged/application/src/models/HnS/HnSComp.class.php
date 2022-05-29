<?php

class HnsComp 
{
    const TABLE = 'comp';
    const ID   = 'id_comp';
    const NOM  = 'nomcomp';
    const TYPE = 'typecomp';

    const TYPE_ACTIVE = 'active';
    const TYPE_PASSIVE = 'passive';
    
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
        $this->Skill = $Skill;
        $this->Order = $Order;
        $this->Rune = $Rune;
        $this->Type = $Type;
    }

    public function __toString ( )
    {
        return 'Comp ' . $this->Type . ' #' . $this->Order . ' ' . $this->Skill . ( '' != $this->Rune ? ' with ' . $this->Rune : '' );
    }

    public function setSkill ( $NewValue ) { $this->Skill = $NewValue; }
    public function setOrder ( $NewValue ) { $this->Order = $NewValue; }
    public function setRune  ( $NewValue ) { $this->Rune  = $NewValue; }
    public function setType  ( $NewValue ) { $this->Type  = $NewValue; }
    
    public function getSkill ( ) { return $this->Skill; }
    public function getOrder ( ) { return $this->Order; }
    public function getRune  ( ) { return $this->Rune ; }
    public function getType  ( ) { return $this->Type ; }
    
    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        // #1 vérifie si un Utilisateur existe pour ce nom
        TagedDBHnS::execute ( "SELECT * FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Skill ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entrée Utilisateur
            TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOM . ", " . self::TYPE . ") VALUES ('" . $this->Skill . "', '" . $this->Type . "');" );
        }

        Log::fct_exit ( __METHOD__ );
    }
    
}
