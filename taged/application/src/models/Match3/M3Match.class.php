<?php

/**
 * Match 3 Match Data
 * @package TAGED\Models\Match3
 */
class M3Match
{
    const TABLE = 'beam';
    
    const ID = 'id_beam';
    const NUM = 'num_match';
    const COLOR = 'couleur';
    const LENGTH = 'longueur';
    const SHAPE = 'forme';
    const SCORE = 'score';
    const SCORE_TOTAL = 'score_total';
    const BEAM = 'barre';
    const TIME = 'temps';
    const TIME_LEFT = 'temps_restant';
    const IN_GAME_TIME = 'temps_en_jeu';
    
    const WS_MATCH_NUM = "match_num";
    const WS_COLOR = "color";
    const WS_LENGTH = "length";
    const WS_SHAPE = "shape";
    const WS_SCORE = "score";
    const WS_SCORE_TOTAL = "score_total";
    const WS_SPECIAL_FOUR = "special_four";
    const WS_BEAM = "beam";
    const WS_TIME = "time";
    const WS_TIME_LEFT = "time_left";
    const WS_IN_GAME_TIME = "in_game_time";
    
    protected $IDMatch;
    protected $MatchNum;
    protected $Color;
    protected $Length;
    protected $Shape;
    protected $Score;
    protected $ScoreTotal;
    protected $SpecialFour;
    protected $Beam;
    protected $Time;
    protected $TimeLeft;
    protected $TimeInGame;
    
    /**
     */
    public function __construct ( 
        $IDMatch = 0,
        $MatchNum = 0,
        $Color = '',
        $Length = 0,
        $Shape = '',
        $Score = 0,
        $ScoreTotal = 0,
        $SpecialFour = '',
        $Beam = '',
        $Time = 0.0,
        $TimeLeft = 0.0,
        $TimeInGame = 0.0
        )
    {
        $this->IDMatch     = $IDMatch;
        $this->MatchNum    = $MatchNum;
        $this->Color       = $Color;
        $this->Length      = $Length;
        $this->Shape       = $Shape;
        $this->Score       = $Score;
        $this->ScoreTotal  = $ScoreTotal;
        $this->SpecialFour = $SpecialFour;
        $this->Beam        = $Beam;
        $this->Time        = $Time;
        $this->TimeLeft    = $TimeLeft;
        $this->TimeInGame  = $TimeInGame;
        
        if ( $IDMatch != 0 )
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
        
        $String .= 'ID ' . $this->IDMatch . "<br>\n";
        $String .= 'Num ' . $this->MatchNum . "<br>\n";
        $String .= 'Color ' . $this->Color . "<br>\n";
        $String .= 'Length ' . $this->Length . "<br>\n";
        $String .= 'Shape ' . $this->Shape . "<br>\n";
        $String .= 'Score ' . $this->Score . "<br>\n";
        $String .= 'ScoreTotal ' . $this->ScoreTotal . "<br>\n";
        $String .= 'SpecialFour ' . $this->SpecialFour . "<br>\n";
        $String .= 'Beam ' . $this->Beam . "<br>\n";
        $String .= 'Time ' . $this->Time . "<br>\n";
        $String .= 'TimeLeft ' . $this->TimeLeft . "<br>\n";
        $String .= 'TimeInGame ' . $this->TimeInGame . "<br>\n";
        
        return $String;
    }
    
    public static function build ( $Array )
    {
        $Match = NULL;
        if ( ! empty ( $Array ) )
        {
            $MatchNum    = Arrays::getIfSet ( $Array, self::WS_MATCH_NUM,    0 );
            $Color       = Arrays::getIfSet ( $Array, self::WS_COLOR,       '' );
            $Length      = Arrays::getIfSet ( $Array, self::WS_LENGTH,       0 );
            $Shape       = Arrays::getIfSet ( $Array, self::WS_SHAPE,       '' );
            $Score       = Arrays::getIfSet ( $Array, self::WS_SCORE,        0 );
            $ScoreTotal  = Arrays::getIfSet ( $Array, self::WS_SCORE_TOTAL,  0 );
            $SpecialFour = Arrays::getIfSet ( $Array, self::WS_SPECIAL_FOUR, 'false' );
            $Beam        = Arrays::getIfSet ( $Array, self::WS_BEAM,         'false' );
            $Time        = Arrays::getIfSet ( $Array, self::WS_TIME,         0.0 );
            $TimeLeft    = Arrays::getIfSet ( $Array, self::WS_TIME_LEFT,    0.0 );
            $TimeInGame  = Arrays::getIfSet ( $Array, self::WS_IN_GAME_TIME, 0.0 );
            
            $Match = new self ( 0, $MatchNum, $Color, $Length, $Shape,
                $Score, $ScoreTotal, $SpecialFour, $Beam,
                $Time, $TimeLeft, $TimeInGame );
        }
        return $Match;
    }
    
    public function getNum ( )
    {
        return $this->MatchNum;
    }
    
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );
        
        Log::fct_exit ( __METHOD__ );
    }
    
    private function fetchId ( $IDStroke )
    {
        $this->IDMatch = 0;
        
        TagedDBMatch3::execute ( "SELECT " . self::ID . " FROM " . self::TABLE
            . " WHERE " . M3Stroke::ID . " = " . $IDStroke
            . " AND " . self::NUM . " = " . $this->MatchNum
            );
        $Results = TagedDBMatch3::getResults ( );
        
        if ( ( NULL !== $Results ) && ( count ( $Results ) > 0 ) )
        {
            $this->IDMatch = $Results [0] [ self::ID ];
        }
    }
    
    public function save ( $IDStroke = 0 )
    {
        Log::fct_enter ( __METHOD__ );

        $this->fetchId ( $IDStroke );
        
        if ( 0 == $this->IDMatch )
        {
            // #2 Si non, ajoute entr�e Utilisateur
            TagedDBMatch3::execute ( "INSERT INTO " . self::TABLE . " (" . 
                self::NUM . ", " . 
                M3Stroke::ID . ", " .
                self::COLOR . ", " .
                self::LENGTH . ", " .
                self::SHAPE . ", " .
                self::SCORE . ", " .
                self::SCORE_TOTAL . ", " .
                self::BEAM . ", " .
                self::TIME . ", " .
                self::TIME_LEFT . ", " .
                self::IN_GAME_TIME .  
                ") VALUES (" .
                $this->MatchNum . ", " .
                $IDStroke . ", " .
                "'" . $this->Color . "'" . ", " .
                $this->Length . ", " .
                "'" . $this->Shape . "'" . ", " .
                $this->Score . ", " .
                $this->ScoreTotal . ", " .
                "'" . $this->Beam . "'" . ", " .
                $this->Time .  ", " .
                $this->TimeLeft . ", " .
                $this->TimeInGame . ");" );
            
            $this->fetchId ( $IDStroke );
        }
        
        Log::fct_exit ( __METHOD__ );
    }
}

