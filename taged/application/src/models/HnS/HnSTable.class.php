<?php

/**
 * Hack'N Slash Table presentation
 * @package TAGED\Models\HackNSlash
 */
class HnSTable
{
    protected $ListeHeros;
    
    /**
     */
    public function __construct()
    {
        $this->ListeHeros = array ();
        $this->getHeros ();
    }

    /**
     */
    public function __destruct()
    {
        foreach ( $this->ListeHeros as $Hero )
        {
            unset ( $Hero ); 
        }
        $this->ListeHeros = array ();
    }
    
    public function __toString ()
    {
        $String = '';
        
        return $String;
    }
    
    public function show ()
    {
        $Content  = HTML::div ( count ( $this->ListeHeros ) . ' h&eacute;ros', array ( 'class' => 'hero_number' ) );
        $Content .= Hero::showTableHeader ();
        
        foreach ( $this->ListeHeros as $Combat )
        {
            $Content .= $Combat->showAsTableEntry ();
        }
        $Content .= Hero::showTableFooter ();
        return $Content;
    }
    
    protected function getHeros ()
    {
        Log::fct_enter ( __METHOD__ );
        // #1 Enregistrer entr�e Combat
        TagedDBHnS::execute ( "SELECT * FROM " . Hero::VIEW . ";" );
        
        $Results = TagedDBHnS::getResults ( );
        foreach ( $Results as $Result )
        {
            $HeroName = Arrays::getIfSet ( $Result, Hero::NOM,      '' );
            $UserName = Arrays::getIfSet ( $Result, HnSPlayer::NOM,   '' );
            $URL      = '';
            $Rank     = Arrays::getIfSet ( $Result, Hero::RANK,  9999 );
            $Rift     = Arrays::getIfSet ( $Result, Hero::RIFT,    0 );
            $Time     = Arrays::getIfSet ( $Result, Hero::TIME,    0 );
            
            $Hero = new Hero ( $HeroName, $UserName, $URL, $Rank, $Rift, $Time );

            $Hero->setServer     ( Arrays::getIfSet ( $Result, Hero::SERVER,    '' ) );
            $Hero->setClass      ( Arrays::getIfSet ( $Result, Hero::CLASSE,    '' ) );
            
            $Hero->setId         ( Arrays::getIfSet ( $Result, Hero::ID,         0 ) );
            $Hero->setLevel      ( Arrays::getIfSet ( $Result, Hero::LEVEL,      0 ) );
            $Hero->setParangon   ( Arrays::getIfSet ( $Result, Hero::PARANGON,   0 ) );
            $Hero->setFor        ( Arrays::getIfSet ( $Result, Hero::FOR,        0 ) );
            $Hero->setDex        ( Arrays::getIfSet ( $Result, Hero::DEX,        0 ) );
            $Hero->setIntel      ( Arrays::getIfSet ( $Result, Hero::INTEL,      0 ) );
            $Hero->setVita       ( Arrays::getIfSet ( $Result, Hero::VITA,       0 ) );
            $Hero->setDegats     ( Arrays::getIfSet ( $Result, Hero::DEGATS,     0 ) );
            $Hero->setRobustesse ( Arrays::getIfSet ( $Result, Hero::ROBUSTESSE, 0 ) );
            $Hero->setRegen      ( Arrays::getIfSet ( $Result, Hero::REGEN,      0 ) );
            $Hero->setRessource1 ( Arrays::getIfSet ( $Result, Hero::RES1,       0 ) );
            $Hero->setRessource2 ( Arrays::getIfSet ( $Result, Hero::RES2,       0 ) );
            $Hero->setVie        ( Arrays::getIfSet ( $Result, Hero::VIE,        0 ) );

            $Hero->getPlayer ()->setId   ( Arrays::getIfSet ( $Result, HnSPlayer::ID,         0 ) );
            $Hero->getPlayer ()->setTag  ( Arrays::getIfSet ( $Result, HnSPlayer::TAG,  '#0000' ) );
            $Hero->getPlayer ()->setClan ( Arrays::getIfSet ( $Result, HnSPlayer::CLAN,      '' ) );
            
            $this->ListeHeros[] = $Hero;
        }
        
        Log::fct_exit ( __METHOD__ );
    }
}

