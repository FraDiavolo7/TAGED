<?php

class Hero 
{
    const VIEW       = 'vw_hero';
    const TABLE      = 'perso';
    const ID         = 'id_perso';
    const NOM        = 'nomperso';
    const SERVER     = 'serveur';
    const RANK       = 'rang';
    const RIFT       = 'faille';
    const TIME       = 'tempsfaille';
    const CLASSE     = 'classe';
    const LEVEL      = 'niveau';
    const PARANGON   = 'parangon';
    const FOR        = 'force';
    const DEX        = 'dexterite';
    const INTEL      = 'intelligence';
    const VITA       = 'vitalite';
    const DEGATS     = 'degats';
    const ROBUSTESSE = 'robustesse';
    const REGEN      = 'recuperation';
    const VIE        = 'vie';
    const RES1       = 'ressource_principale';
    const RES2       = 'ressource_secondaire';

    
    const ATTR_FOR        = 'Strength';
    const ATTR_DEX        = 'Dexterity';
    const ATTR_INTEL      = 'Intelligence';
    const ATTR_VITA       = 'Vitality';
    const ATTR_DEGATS     = 'Damage';
    const ATTR_ROBUSTESSE = 'Toughness';
    const ATTR_REGEN      = 'Recovery';
    const ATTR_VIE        = 'Life';
    const ATTR_RES_B      = 'Fury';
    const ATTR_RES_W      = 'Essence';
    const ATTR_RES_WD     = 'Mana';
    const ATTR_RES_C      = 'Wrath';
    const ATTR_RES_M      = 'Spirit';
    const ATTR_RES_DH     = 'Hatred/ Discipline';
    
    private $Id;
    private $Heroname;
    private $Player;
    private $URL;
    private $Server;
    private $Class;
    private $Rank;
    private $Rift;
    private $TimeStr;
    private $Time;
    private $Level;
    private $Parangon;
    private $For;
    private $Dex;
    private $Intel;
    private $Vita;
    private $Degats;
    private $Robustesse;
    private $Regen;
    private $Ressource1;
    private $Ressource2;
    private $Vie;
    
    private $Comps;
    private $Items;
    private $CurrentItem;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Hero = '', $Username = '', $URL = '', $Rank = 9999, $Rift = 0, $Time = '' ) 
    {
        $this->Player = new HnSPlayer ( $Username );
        $this->Heroname = $Hero;
        $this->URL = $URL;
        $this->Server = 'pq';
        $this->Class = 'class';
        $this->Rank = $Rank;
        $this->Rift = $Rift;
        $this->setTime ( $Time );
        $this->Level = 0;
        $this->Parangon = 0;
        $this->Comps = array ();
        $this->Items = array ();
        $this->CurrentItem = 0;
        $this->For        = 0;
        $this->Dex        = 0;
        $this->Intel      = 0;
        $this->Vita       = 0;
        $this->Degats     = 0;
        $this->Robustesse = 0;
        $this->Regen      = 0;
        $this->Ressource1 = 0;
        $this->Ressource2 = 0;
        $this->Vie        = 0;
    }
   
    public static function mark4DL ( $Array, $BaseURL, $Server, $HeroClass )
    {
        $Rank     = Arrays::getOrCrash ( $Array, 1, 'Invalid Hero position' );
        $Username = Arrays::getOrCrash ( $Array, 3, 'Invalid Player name'     );
        $URL      = $BaseURL . Arrays::getOrCrash ( $Array, 2, 'Invalid hero URL'     );
        $Rift     = Arrays::getIfSet   ( $Array, 4, ''  );
        $Time     = Arrays::getIfSet   ( $Array, 5, ''  );
        $TimeArr  = explode ( 'm ', $Time );
        $TimeMin  = $TimeArr [0];
        $TimeArrS = explode ( '.', $TimeArr [1] ); 
        $TimeS    = $TimeArrS [0];
        $TimeMS   = $TimeArrS [1];
        $TimeStr  = sprintf ( "%02d-%02d-%03d", $TimeMin, $TimeS, $TimeMS );
        $Folder   = sprintf ( '%s%03d/', DATA_TMP_HNS_ADDR, $Rank % 100 );
        $FileName = sprintf ( '%s_%s_%04d_%03d_%s', $Server, $HeroClass, $Rank, $Rift, $TimeStr );
        echo "marking $FileName $URL<br>\n";
        $FilePath = $Folder . $FileName;

        if ( ! is_dir ( $Folder ) ) mkdir ( $Folder, 0777, true );

        file_put_contents ( $FilePath, $URL );
    }

    public function __toString ( )
    {
        $Result = 'Hero ' . $this->Heroname . '(' . $this->Level . '(' . $this->Parangon . ')) is #' . $this->Rank . ' ' . $this->Class . ' ' . $this->Server . ' Rift ' . $this->Rift . " in " . $this->Time . "\n";
        $Result .= ' FOR ' . $this->For . "\n";        
        $Result .= ' DEX ' . $this->Dex . "\n";
        $Result .= ' INT ' . $this->Intel . "\n";
        $Result .= ' VIT ' . $this->Vita . "\n";
        $Result .= ' DGT ' . $this->Degats . "\n";
        $Result .= ' ROB ' . $this->Robustesse . "\n";
        $Result .= ' RGN ' . $this->Regen . "\n";
        $Result .= ' VIE ' . $this->Vie . "\n";
        $Result .= ' RES ' . $this->Ressource1 . "\n";
        if ( 0 != $this->Ressource2 )  $Result .= ' RS2 ' . $this->Ressource2 . "\n";
        $Result .= $this->Player . "\n";
        $Result .= Arrays::arrToString ( $this->Comps, '', '', "\n" );
        $Result .= Arrays::arrToString ( $this->Items, '', '', "\n" );
        return $Result;
    }

    public function setAttr       ( $Label, $NewValue ) 
    { 
        $Repl = array ( ',', 'k',   'M' ) ;
        $By   = array ( '',  '000', '000000' ) ;
        $NewValue = str_replace ( $Repl, $By, $NewValue );
        switch ( $Label )
        {
            case self::ATTR_FOR        : $this->setFor        ( $NewValue ); break;
            case self::ATTR_DEX        : $this->setDex        ( $NewValue ); break;
            case self::ATTR_INTEL      : $this->setIntel      ( $NewValue ); break;
            case self::ATTR_VITA       : $this->setVita       ( $NewValue ); break;
            case self::ATTR_DEGATS     : $this->setDegats     ( $NewValue ); break;
            case self::ATTR_ROBUSTESSE : $this->setRobustesse ( $NewValue ); break;
            case self::ATTR_REGEN      : $this->setRegen      ( $NewValue ); break;
            case self::ATTR_VIE        : $this->setVie        ( $NewValue ); break;
            case self::ATTR_RES_B      :
            case self::ATTR_RES_W      :
            case self::ATTR_RES_WD     :
            case self::ATTR_RES_C      :
            case self::ATTR_RES_M      : $this->setRessource1 ( $NewValue ); break;
            case self::ATTR_RES_DH     : $this->setResources ( $NewValue ); break;
            
            default : 
                throw new Exception ( 'Attribut inconnu ' . $Label );
        }
    }
    
    public function setResources ( $NewValue ) 
    { 
        $Resources = explode ( '<br />', $NewValue );
        $this->setRessource1 ( $Resources [0] );     
        $this->setRessource2 ( $Resources [1] );
    }
    
    public function setTime     ( $NewValue ) 
    { 
        $this->TimeStr     = $NewValue; 
        if ( '' != $NewValue )
        {
            $Time = explode ( '-', $this->TimeStr );
            $this->Time = $Time [0] * 60000 + $Time [1] * 1000 + $Time [2];
        }
        else
        {
            $this->Time = 0;
        }
    }

    public function setId       ( $NewValue ) { $this->Id       = $NewValue; }
    public function setHeroname ( $NewValue ) { $this->Heroname = $NewValue; }
    public function setURL      ( $NewValue ) { $this->URL      = $NewValue; }
    public function setServer   ( $NewValue ) { $this->Server   = $NewValue; }
    public function setClass    ( $NewValue ) { $this->Class    = $NewValue; }
    public function setRank     ( $NewValue ) { $this->Rank     = $NewValue; }
    public function setRift     ( $NewValue ) { $this->Rift     = $NewValue; }
    public function setLevel    ( $NewValue ) { $this->Level    = $NewValue; }
    public function setParangon ( $NewValue ) { $this->Parangon = str_replace ( ',', '', $NewValue ); }
    public function addComp     ( $NewValue ) { $this->Comps [] = $NewValue; }
    public function addItem     ( $NewValue ) { $this->CurrentItem += 1; $this->Items [ $this->CurrentItem ] = $NewValue; }
    public function addAffix    ( $NewValue ) { $this->Items [ $this->CurrentItem ]->addAffix ( $NewValue ); }
    
    public function setFor        ( $NewValue ) { $this->For        = $NewValue; }
    public function setDex        ( $NewValue ) { $this->Dex        = $NewValue; }
    public function setIntel      ( $NewValue ) { $this->Intel      = $NewValue; }
    public function setVita       ( $NewValue ) { $this->Vita       = $NewValue; }
    public function setDegats     ( $NewValue ) { $this->Degats     = $NewValue; }
    public function setRobustesse ( $NewValue ) { $this->Robustesse = $NewValue; }
    public function setRegen      ( $NewValue ) { $this->Regen      = $NewValue; }
    public function setRessource1 ( $NewValue ) { $this->Ressource1 = $NewValue; }
    public function setRessource2 ( $NewValue ) { $this->Ressource2 = $NewValue; }
    public function setVie        ( $NewValue ) { $this->Vie        = $NewValue; }
    
    public function getHeroname ( ) { return $this->Heroname; }
    public function getURL      ( ) { return $this->URL     ; }
    public function getServer   ( ) { return $this->Server  ; }
    public function getClass    ( ) { return $this->Class   ; }
    public function getRank     ( ) { return $this->Rank    ; }
    public function getRift     ( ) { return $this->Rift    ; }
    public function getTime     ( ) { return $this->Time    ; }
    public function getLevel    ( ) { return $this->Level   ; }
    public function getParangon ( ) { return $this->Parangon; }
    
    public function getFor        () { return $this->For       ; }
    public function getDex        () { return $this->Dex       ; }
    public function getIntel      () { return $this->Intel     ; }
    public function getVita       () { return $this->Vita      ; }
    public function getDegats     () { return $this->Degats    ; }
    public function getRobustesse () { return $this->Robustesse; }
    public function getRegen      () { return $this->Regen     ; }
    public function getRessource1 () { return $this->Ressource1; }
    public function getRessource2 () { return $this->Ressource2; }
    public function getVie        () { return $this->Vie       ; }
    
    public function getPlayer   ( ) { return $this->Player; }
    public function getItems    ( ) { return $this->Items ; }
    public function getCurItem  ( ) { return $this->Items [ $this->CurrentItem ] ; }
    public function getItem     ( $ItemId ) { return $this->Items [ $ItemId ] ; }
    
    public function getComps    ( ) { return $this->Comps ; }

    public function getId  ( )
    {
        if ( -1 == $this->Id ) $this->fetchId ();
        return $this->Id ;
    }

    
    public static function showTableHeader ()
    {
        $Content  = HTML::startTable ();
        $Content .= HTML::startTR ();
        $Content .= HTML::th ( 'Player' );
        $Content .= HTML::th ( 'Hero' );
        $Content .= HTML::th ( 'Server' );
        $Content .= HTML::th ( 'Class' );
        $Content .= HTML::th ( 'Level' );
        $Content .= HTML::th ( 'Parangon' );
        $Content .= HTML::th ( 'Rank' );
        $Content .= HTML::th ( 'Rift' );
        $Content .= HTML::th ( 'Time' );
        $Content .= HTML::endTR ();
        return $Content;
    }
    
    public static function showTableFooter ()
    {
        return HTML::endTable ();
    }
    
    public function showAsTableEntry ()
    {
        $Content  = HTML::startTR ();
        $Content .= HTML::td ( $this->Player );
        $Content .= HTML::td ( $this->Heroname );
        $Content .= HTML::td ( $this->Server );
        $Content .= HTML::td ( $this->Class );
        $Content .= HTML::td ( $this->Level );
        $Content .= HTML::td ( $this->Parangon );
        $Content .= HTML::td ( $this->Rank );
        $Content .= HTML::td ( $this->Rift );
        $Content .= HTML::td ( $this->Time );
        $Content .= HTML::endTR ();
        return $Content;
    }
    
    
    private function fetchId ()
    {
        $this->Id = -1;

        TagedDBHnS::execute ( "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOM . " = '" . $this->Heroname ."'" );
        $Results = TagedDBHnS::getResults ( );

        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->Id = $Results [0] [ self::ID ];
        }
    }

    private function saveComps ()
    {
        foreach ( $this->Comps as $Comp )
        {
            $Comp->save ( $this->Id );
        }
    }

    private function saveItems ()
    {
        foreach ( $this->Items as $Item )
        {
            $Item->save ( $this->Id );
        }
    }

    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        $this->Player->save ();

        $PlId = $this->Player->getId ();

        TagedDBHnS::execute ( "INSERT INTO " . self::TABLE . " (" . 
            self::NOM . ", " . 
            self::SERVER . ", " .
            self::RANK . ", " .
            self::RIFT . ", " .
            self::TIME . ", " .
            self::CLASSE . ", " .
            self::LEVEL . ", " .
            self::PARANGON . ", " .
            self::FOR . ", " .
            self::DEX . ", " .
            self::INTEL . ", " .
            self::VITA . ", " .
            self::DEGATS . ", " .
            self::ROBUSTESSE . ", " .
            self::REGEN . ", " .
            self::VIE . ", " .
            self::RES1 . ", " .
            self::RES2 . ", " .
            HnSPlayer::ID . 
            ") VALUES (" . 
            "'" . $this->Heroname   . "', " .
            "'" . $this->Server     . "', " .
            ""  . $this->Rank       . ", " .
            ""  . $this->Rift       . ", " .
            ""  . $this->Time       . ", " .
            "'" . $this->Class      . "', " .
            ""  . $this->Level      . ", " .
            ""  . $this->Parangon   . ", " .
            ""  . $this->For        . ", " .
            ""  . $this->Dex        . ", " .
            ""  . $this->Intel      . ", " .
            ""  . $this->Vita       . ", " .
            ""  . $this->Degats     . ", " .
            ""  . $this->Robustesse . ", " .
            ""  . $this->Regen      . ", " .
            ""  . $this->Vie        . ", " .
            ""  . $this->Ressource1 . ", " .
            ""  . $this->Ressource2 . ", " .
            ""  . $PlId . "" .
            ");" );

        $this->fetchId ();

        $this->saveComps ();
        $this->saveItems ();

        Log::fct_exit ( __METHOD__ );
    }
}
