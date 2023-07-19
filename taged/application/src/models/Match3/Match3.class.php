<?php

/**
 * Match 3 Main
 * @package TAGED\Models\Match3
 */
class Match3
{
    protected $ListGames;
    
    /**
     */
    public function __construct()
    {
        $this->$ListGames = array ();
        $this->getGames ();
    }

    /**
     */
    public function __destruct()
    {
        foreach ( $this->$ListGames as $Game )
        {
            unset ( $Game ); 
        }
        $this->$ListGames = array ();
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
}

