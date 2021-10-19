<?php

/**
 *
 * @author Fra Diavolo
 *        
 */
class Game
{
    protected $Player1;
    protected $Player2;
    protected $Team1;
    protected $Team2;
    protected $Result; // 0 = tie, 1 = P1 wins, 2 = P2 wins
    protected $Tier;
    protected $Turns;
    protected $Rules;
    protected $Type;
    protected $Rating;
    
    /**
     */
    public function __construct()
    {
        $this->Result = 0;
        $this->GameType = '';
        $this->Gen      = '';
        $this->Tier     = '';
        $this->Rating   = '';
    }

    /**
     */
    public function __destruct()
    {
        unset ( $this->Player1 );
        unset ( $this->Player2 );
        unset ( $this->Team1 );
        unset ( $this->Team2 );
    }
    
    public function __toString ()
    {
        $String = '';
        
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
    
    
    public function check ( )
    {
        if ( NULL == $this->Player1 ) throw new Exception ( 'Player 1 not provided'     );
        if ( NULL == $this->Player2 ) throw new Exception ( 'Player 2 not provided'     );
        if ( NULL == $this->Team1   ) throw new Exception ( 'Team 1 not provided'       );
        if ( NULL == $this->Team2   ) throw new Exception ( 'Team 2 not provided'       );
        if ( ''   == $this->Type    ) throw new Exception ( 'Game Type not provided'    );
        if ( ''   == $this->Gen     ) throw new Exception ( 'Generation not provided'   );
        if ( ''   == $this->Tier    ) throw new Exception ( 'Tier not provided'         );
        if ( ''   == $this->Rating  ) $this->Rating = false;
    }
    
    public function setPlayer ( $Player ) 
    {
        if ( 1 == $Player->getPlayer () )
        {
            $this->Player1 = $Player;
        }
        else
        {
            $this->Player2 = $Player;
        }
    }
    
    public function setTeam ( $Team )
    {
        if ( 1 == $Team->getPlayer () )
        {
            $this->Team1 = $Team;
        }
        else
        {
            $this->Team2 = $Team;
        }
    }
    
    public function setType ( $GameType )
    {
        $this->Type = $GameType;
    }
    
    public function setGen ( $Gen )
    {
        $this->Gen = $Gen;
    }
    
    public function setTier ( $Tier )
    {
        $this->Tier = $Tier;
    }
    
    public function setRated ( $Message = "" )
    {
        $this->Rating = ( $Message != "" ? true : false );
        $this->RatingMessage = $Message;
    }
    
    public function setWinner ( $Player )
    {
        if ( $this->Player1->getUsername () == $Player ) $this->Result = 1;
        else $this->Result = 2;
    }

    public function setTie ( )
    {
        $this->Result = 0;
    }
    
    public function switch ( $Player, $Pokemon )
    {
        echo "Switch $Player $Pokemon <br>\n";
        if ( 1 == $Player )
        {
            $this->Team1->switch ( $Pokemon );
        }
        else
        {
            $this->Team2->switch ( $Pokemon );
        }
    }
}

