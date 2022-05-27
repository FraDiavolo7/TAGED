<?php

class HnsComp 
{
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
    
    
}
