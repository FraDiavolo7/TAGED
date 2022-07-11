<?php

class M3Game
{
    const TABLE_PLAYER = 'joueur';
    const TABLE_GAME = 'partie';
    
    const VIEW_DATA = 'vw_m3_data';
    const VIEW_STAT = 'vw_m3_stat';
    
    const ID_PLAYER = 'id_joueur';
    const ID_GAME = 'id_partie';
    const IP_ADDR = 'ip_addr';
    const DATE_GAME = 'date_partie';
    const NUM_ROUND = 'num_tour';
    const DATE_ROUND = 'date_tour';
    
    const COUNT_PLAYER = 'count_joueur';
    const COUNT_GAME   = 'count_partie';
    const COUNT_STROKE = 'count_coup';
    const COUNT_MATCH  = 'count_beam';
    
    const WS_PLAYER = 'player';
    const WS_PLAYER_ID = 'player_id';
    const WS_PLAYER_ADDR = 'ipaddress';
    const WS_GAME = 'game';
    const WS_GAME_DATE = 'game_date';
    const WS_ROUND = 'round';
    const WS_ROUND_NUM = 'round_num';
    const WS_ROUND_DATE = 'round_date';
    const WS_STROKE = 'stroke';
    
    const COUNT_TRAD = array (
        self::COUNT_PLAYER => 'Joueurs',
        self::COUNT_GAME   => 'Parties',
        self::COUNT_STROKE => 'Coups',
        self::COUNT_MATCH  => 'Matchs'
    );
    
    protected $GameID;
    protected $PlayerID;
    protected $PlayerIP;
    protected $GameDate;
    protected $RoundDate;
    protected $RoundNum;
    protected $Strokes;
    
    /**
     */
    public function __construct ( 
        $IDPlayer = '',
        $PlayerIP = '',
        $GameDate =  0,
        $RoundDate = 0,
        $RoundNum  = 0,
        $Strokes = array ()
        )
    {
        $this->PlayerID  = $IDPlayer;
        $this->PlayerIP  = $PlayerIP;
        $this->GameDate  = $GameDate;
        $this->RoundDate = $RoundDate;
        $this->RoundNum  = $RoundNum;
        $this->Strokes = array ();
        
        foreach ( $Strokes as $Stroke )
        {
            $this->addStroke ( $Stroke );
        }
        
        if ( $IDPlayer != '' )
        {
            $this->fill ();
        }
    }

    /**
     */
    public function __destruct()
    {
        unset ( $this->Strokes );
    }
    
    public function __toString ()
    {
        $String = '';
        
        $String .= 'ID ' . $this->PlayerID . "<br>\n";
        $String .= 'IP ' . $this->PlayerIP . "<br>\n";
        $String .= 'Date ' . $this->GameDate . "<br>\n";
        $String .= 'Round ' . $this->RoundNum . ' (' . $this->RoundDate . ')' . "<br>\n";
        
        foreach ( $this->Strokes as $Stroke )
        {
            $String .= 'Stroke ' . $Stroke . "<br>\n";
        }
        
        return $String;
    }
    
    public static function build ( $Array )
    {
        $Game = NULL;
        if ( ! empty ( $Array ) )
        {
            $Player = Arrays::getIfSet ( $Array, self::WS_PLAYER, array () );
            $Game   = Arrays::getIfSet ( $Array, self::WS_GAME,   array () );
            $Round  = Arrays::getIfSet ( $Array, self::WS_ROUND,  array () );
    
            $PlayerID  = Arrays::getIfSet ( $Player, self::WS_PLAYER_ID,   '' );
            $PlayerIP  = Arrays::getIfSet ( $Player, self::WS_PLAYER_ADDR, '' );
            
            $GameDate  = Arrays::getIfSet ( $Game,   self::WS_GAME_DATE,   0 );
    
            $RoundDate = Arrays::getIfSet ( $Round,  self::WS_ROUND_DATE,  0 );
            $RoundNum  = Arrays::getIfSet ( $Round,  self::WS_ROUND_NUM,   0 );
            $Strokes   = Arrays::getIfSet ( $Round,  self::WS_STROKE,      array () );
            
            $Game = new self ( $PlayerID, $PlayerIP, $GameDate, $RoundDate, $RoundNum, $Strokes );
        }
        return $Game;
    }
    
    protected function addStroke ( $StrokeData = array () )
    {
        if ( ! empty ( $StrokeData ) )
        {
            $Stroke = M3Stroke::build ( $StrokeData );
            
            $this->Strokes [ $Stroke->getNum () ] = $Stroke;
        }
    }
    
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );
        
        Log::fct_exit ( __METHOD__ );
    }

   
    public function savePlayer ( )
    {
        TagedDBMatch3::execute ( "SELECT * FROM " . self::TABLE_PLAYER . " WHERE " . self::ID_PLAYER . " = '" . $this->PlayerID . "'" );
        $Results = TagedDBMatch3::getResults ( );
        
        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entree Player
            TagedDBMatch3::execute ( "INSERT INTO " . self::TABLE_PLAYER . " (" . self::ID_PLAYER . ", " . self::IP_ADDR . ") VALUES ('" . $this->PlayerID . "', '" . $this->PlayerIP . "');" );
        }
    }
    
    private function fetchGameId ()
    {
        $this->GameID = 0;
        
        TagedDBMatch3::execute ( "SELECT " . self::ID_GAME . " FROM " . self::TABLE_GAME
            . " WHERE " . self::ID_PLAYER . " = '" . $this->PlayerID . "'"
            . " AND " . self::DATE_GAME . " = " . $this->GameDate
            . " AND " . self::DATE_ROUND . " = " . $this->RoundDate
            . " AND " . self::NUM_ROUND . " = " . $this->RoundNum
            );
        $Results = TagedDBMatch3::getResults ( );
        
        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->GameID = $Results [0] [ self::ID_GAME ];
        }
    }
    
    public static function getStats ()
    {
        $Stats = array ();
        
        TagedDBMatch3::execute ( "SELECT * FROM " . self::VIEW_STAT );
        $Results = TagedDBMatch3::getResults ( );
        
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
    
    
    public function saveGame ( )
    {
        $this->fetchGameId ( );
        
        if ( 0 == $this->GameID )
        {
            // #2 Si non, ajoute entree Player
            TagedDBMatch3::execute ( "INSERT INTO " . self::TABLE_GAME . " ("
                . self::DATE_GAME . ", " 
                . self::DATE_ROUND . ", "
                . self::NUM_ROUND . ", "
                . self::ID_PLAYER
                . ") VALUES ("
                . $this->GameDate . ", "
                . $this->RoundDate . ", "
                . $this->RoundNum . ", "
                . "'" . $this->PlayerID . "'"
                . ");" );
        
            $this->fetchGameId ();
        }
    }
    
    public function save ( )
    {
        Log::fct_enter ( __METHOD__ );
        
        $this->savePlayer ( );
        $this->saveGame ( );
        
        foreach ( $this->Strokes as $Stroke )
        {
            $Stroke->save ( $this->GameID );
        }
        
        Log::fct_exit ( __METHOD__ );
    }
}

