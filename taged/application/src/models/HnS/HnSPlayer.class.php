<?php

class HnSPlayer 
{
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
        return 'Player ' . $this->Username . ' is ' . $this->Tag . ( '' != $this->Clan ? ' from ' . $this->Clan : '' );
    }

    public function setUsername ( $NewValue ) { $this->Username = $NewValue; }
    public function setTag      ( $NewValue ) { $this->Tag      = $NewValue; }
    public function setClan     ( $NewValue ) { $this->Clan     = $NewValue; }
    
    public function getUsername ( ) { return $this->Username; }
    public function getTag      ( ) { return $this->Tag     ; }
    public function getClan     ( ) { return $this->Clan    ; }
    
    
}
