<?php

class HnsItem 
{
    private $Position;
    private $Image;
    private $Name;
    private $Affix;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Position = '', $Image = '', $Name = '', $Affix = array () ) 
    {
        $this->Position = $Position;
        $this->Image = $Image;
        $this->Name = $Name;
        $this->Affix = $Affix;
    }

    public function __toString ( )
    {
        $Result = 'Item ' . $this->Name . ' on ' . $this->Position;
        $Sep = ' with ';

        foreach ( $this->Affix as $Affix )
        {
            $Result .= $Sep . $Affix;
            $Sep = ', ';
        }
        
        return $Result;
    }

    public function setPosition ( $NewValue ) { $this->Position = $NewValue; }
    public function setImage    ( $NewValue ) { $this->Image    = $NewValue; }
    public function setName     ( $NewValue ) { $this->Name     = $NewValue; }
    public function setAffix    ( $NewValue ) { $this->Affix    = $NewValue; }
    public function addAffix    ( $NewValue ) { $this->Affix [] = $NewValue; }
    
    public function getPosition ( ) { return $this->Position; }
    public function getImage    ( ) { return $this->Image   ; }
    public function getName     ( ) { return $this->Name    ; }
    public function getAffix    ( ) { return $this->Affix   ; }
    
    
}
