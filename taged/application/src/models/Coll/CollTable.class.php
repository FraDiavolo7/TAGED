<?php

/**
 * Collection Table presentation
 * @package TAGED\Models\Collection
 */
class CollTable
{
    const VIEW_STAT = 'vw_coll_stat';
    
    const COUNT_POKEMON  = 'count_pokemon';
    const COUNT_EQUIPE  = 'count_equipe';
    const COUNT_COMBAT  = 'count_combat';
    const COUNT_UTILISATEUR  = 'count_utilisateur';
    
    const COUNT_TRAD = array (
        self::COUNT_POKEMON => 'Pokemons',
        self::COUNT_EQUIPE => '&Eacute;quipes',
        self::COUNT_COMBAT => 'Combats',
        self::COUNT_UTILISATEUR => 'Utilisateurs',
    );
    
    protected $ListeCombats;
    
    /**
     */
    public function __construct()
    {
        $this->ListeCombats = array ();
        $this->getGames ();
    }

    /**
     */
    public function __destruct()
    {
        foreach ( $this->ListeCombats as $Game )
        {
            unset ( $Game ); 
        }
        $this->ListeCombats = array ();
    }
    
    public function __toString ()
    {
        $String = '';
        
        $String .= $this->Player1 . "<br>\n";
        $String .= $this->Team1 . "<br>\n";
        $String .= $this->Player2 . "<br>\n";
        $String .= $this->Team2 . "<br>\n";
        $String .= $this->Type . "<br>\n";
        $String .= $this->Gen . "<br>\n";
        $String .= $this->Tier . "<br>\n";
        $String .= $this->Rating . "<br>\n";
        //         $String .= 'Rules ' . $this->Rules . "<br>\n";
        //         $String .= 'Team Preview ' . $this->TeamPreview . "<br>\n";
        
        switch ( $this->Result )
        {
            case 0: $String .= "Game tied<br>\n"; break;
            case 1: $String .= $this->Player1->getUsername () . " wins<br>\n"; break;
            case 2: $String .= $this->Player2->getUsername () . " wins<br>\n"; break;
        }
        return $String;
    }
    
    public function show ()
    {
        $Content  = HTML::div ( count ( $this->ListeCombats ) . ' combats', array ( 'class' => 'combat_number' ) );
        $Content .= CollGame::showTableHeader ();
        
        foreach ( $this->ListeCombats as $Combat )
        {
            $Content .= $Combat->showAsTableEntry ();
        }
        $Content .= CollGame::showTableFooter ();
        return $Content;
    }
    
    protected function getGames ()
    {
        Log::fct_enter ( __METHOD__ );
        // #1 Enregistrer entr�e Combat
        TagedDBColl::execute ( "SELECT * FROM " . CollGame::TABLE . ";" );
        
        $Results = TagedDBColl::getResults ( );
        foreach ( $Results as $Result )
        {
            $GameID  = Arrays::getIfSet ( $Result, CollGame::ID,     -1 );
            $GameRes = Arrays::getIfSet ( $Result, CollGame::RESULT, '' );
            $Tier    = Arrays::getIfSet ( $Result, CollGame::TIER,   '' );
            $Rules   = Arrays::getIfSet ( $Result, CollGame::RULES,  '' );
            $Classe  = Arrays::getIfSet ( $Result, CollGame::CLASSE, '' );
            $this->ListeCombats[] = new CollGame ( $GameID, '', $Tier, $Classe, $Rules, $GameRes );            
        }
        
        Log::fct_exit ( __METHOD__ );
    }
    
    public static function getStats ()
    {
        $Stats = array ();
        
        TagedDBColl::execute ( "SELECT * FROM " . self::VIEW_STAT );
        $Results = TagedDBColl::getResults ( );
        
        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            foreach ( $Results [0] as $Key => $Stat )
            {
                if ( ! is_numeric ( $Key ) )
                {
                    $Stats [0] [ self::COUNT_TRAD [ $Key ] ] = $Stat;
                }
            }
        }
        
        return $Stats;
    }
}

