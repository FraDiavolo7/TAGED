<?php

class M3Stroke
{
    const TABLE = 'coup';
    
    const ID = 'id_coup';
    const NUM = 'num_coup';
    const DURATION = 'duree';
    const TIME = 'heure';
    
    const WS_STROKE_NUM = 'stroke_num';
    const WS_DURATION = 'duration';
    const WS_TIME = 'time';
    const WS_MATCH = 'match';
        
    protected $IDStroke;
    protected $StrokeNum;
    protected $Duration;
    protected $Time;
    protected $Matches;
    
    /**
     */
    public function __construct ( 
        $IDStroke = 0,
        $StrokeNum = 0,
        $Duration = 0,
        $Time =  0,
        $Matches = array ()
        )
    {
        $this->IDStroke = $IDStroke;
        $this->StrokeNum  = $StrokeNum;
        $this->Duration  = $Duration;
        $this->Time  = $Time;
        $this->Matches  = array ();
        
        foreach ( $Matches as $Match )
        {
            $this->addMatch ( $Match );
        }
        
        if ( $IDStroke != 0 )
        {
            $this->fill ();
        }
    }

    /**
     */
    public function __destruct()
    {
    }
    
    public function __toString ()
    {
        $String = '';
        
        $String .= 'ID ' . $this->IDStroke . "<br>\n";
        $String .= 'Num ' . $this->StrokeNum . "<br>\n";
        $String .= 'Duration ' . $this->Duration . "<br>\n";
        $String .= 'Time ' . $this->Time . "<br>\n";
        
        foreach ( $this->Matches as $Match )
        {
            $String .= 'Match ' . $Match . "<br>\n";
        }
        
        return $String;
    }
    
    public static function build ( $Array )
    {
        $Stroke = NULL;
        if ( ! empty ( $Array ) )
        {
            $StrokeNum = Arrays::getIfSet ( $Array, self::WS_STROKE_NUM, 0 );
            $Duration  = Arrays::getIfSet ( $Array, self::WS_DURATION,   0 );
            $Time      = Arrays::getIfSet ( $Array, self::WS_TIME,       0 );
            $Matches   = Arrays::getIfSet ( $Array, self::WS_MATCH,      array () );
            
            $Stroke = new self ( 0, $StrokeNum, $Duration, $Time, $Matches );
        }
        return $Stroke;
    }
    
    public function getNum ( )
    {
        return $this->StrokeNum;
    }
    
    protected function addMatch ( $MatchData = array () )
    {
        if ( ! empty ( $MatchData ) )
        {
            $Match = M3Match::build ( $MatchData );
            
            $this->Matches [ $Match->getNum () ] = $Match;
        }
    }
    
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );
        
        Log::fct_exit ( __METHOD__ );
    }
    
    private function fetchId ( $IDGame )
    {
        $this->IDStroke = 0;
        
        TagedDBMatch3::execute ( "SELECT " . self::ID . " FROM " . self::TABLE
            . " WHERE " . M3Game::ID_GAME . " = " . $IDGame
            . " AND " . self::NUM . " = " . $this->StrokeNum
            );
        $Results = TagedDBMatch3::getResults ( );
        
        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->IDStroke = $Results [0] [ self::ID ];
        }
    }
    
    
    public function save ( $IDGame = 0 )
    {
        Log::fct_enter ( __METHOD__ );
        
        $this->fetchId ( $IDGame );
        
        if ( 0 == $this->IDStroke )
        {
            // #2 Si non, ajoute entree Stroke
            TagedDBMatch3::execute ( "INSERT INTO " . self::TABLE . " (" . self::NUM . ", " . self::DURATION . ", " . self::TIME . ", " . M3Game::ID_GAME . ") VALUES (" . $this->StrokeNum . ", " . $this->Duration . ", " . $this->Time . ", " . $IDGame . ");" );

            $this->fetchId ( $IDGame );
        }
        
        foreach ( $this->Matches as $Match )
        {
            $Match->save ( $this->IDStroke );
        }
        
        Log::fct_exit ( __METHOD__ );
    }
}

