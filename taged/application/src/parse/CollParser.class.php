<?php

class CollParser {

    private $turns;
    
    private $FullText;
    private $ProcessedText;
    private $Game;
    
    public function __construct ( $TextToParse ) 
    {
        $this->FullText = $TextToParse;
        
        $this->Game = new CollGame ();
        $this->clean ();
    }
    
    public function __destruct ( )
    {
        unset ( $this->Game );
    }

    /**
     * @brief Clean the $this->FullText member variable of all undesirables characters.
     * Use because PCRE pattern modifier m (PCRE_MULTILINE) have to use "\n" characters in a subject string to match
     * multiline group (particularly useful for lists).
     */
    private function clean () 
    {
        $tmp = preg_replace('/\R/u', "\n", $this->FullText);
        
        preg_replace_callback ( '/input type="hidden" name="replayid" value="([^"]*)" \//m', array ( $this, "gameIdPreg" ), $tmp );
        
        $this->ProcessedText  = preg_replace('/^.*\<script type="text\/plain" class="battle-log-data"\>(.*)\<\/script\>.*$/sU', '$1', $tmp);
    }

    protected function applyPattern ( $Pattern, $Callback, $Count = 1 )
    {
        // TOTO @CDE Tester utilité.
        // Si Player est appelé 1 fois, pourquoi rule doit être appelé un nombre de fois défini?
        for ( $i = 0 ; $i < $Count ; ++$i )
        {
            $this->ProcessedText = preg_replace_callback ( $Pattern, array ( $this, $Callback ), $this->ProcessedText );
        }
    }

    /**
     *  @brief Parse (with PCRE) the $this->text member variable.
     *  @throws Exception
     */
    public function parse () 
    {
        Log::fct_enter ( __METHOD__ );

        /* ******************************** Battle initialization. ******************************** */
        /* Players. */
        $this->applyPattern ( '/\|player\|p([0-9]+)\|([^\|\n|\r|\f]+)\|([^\|\n|\r|\f]+)(?:\|([^\|\n|\r|\f]+))?(?:\|([^\|\n|\r|\f]+))?(?:\|)?/', 'playerPreg' );

        /* Teams sizes. */
        $this->applyPattern ( '/\|teamsize\|p([0-9]+)\|([0-9])/', 'teamSizePreg' );

        /* Game type. */
        $this->applyPattern ( '/\|gametype\|(.*)/', 'gameTypePreg' );

        /* Gen. */
        $this->applyPattern ( '/\|gen\|(.*)/', 'genPreg' );

        /* Tier. */
        $this->applyPattern ( '/\|tier\|(.*)/', 'tierPreg' );

        /* Rated. */
        $this->applyPattern ( '/\|(rated)\|(.*)/', 'ratedPreg' );

        /* Rule(s). */
        $NbRules = substr_count ( $this->ProcessedText, '|rule|' );
        $this->applyPattern ( '/\|rule\|(.*)/', 'rulePreg', $NbRules );
        
        /* Team preview(s). */
        $NbPreview = substr_count ( $this->ProcessedText, '|poke|' );
        $this->applyPattern ( '/\|poke\|(.*)\|(.*)\|(.*)/', 'teamPreviewPokemonPreg', $NbPreview );

        /* ******************************** Battle progress. ******************************** */

        $this->ProcessedText = preg_replace('/\|start[\|\n|\r|\f](.*)$/sU', '$1', $this->ProcessedText);

        /* First (current) pokemons. */
        $this->applyPattern ( '/^\|switch\|p1a: .*\|([^\|]+)\|.*$/mU', 'switchP1' );
        $this->applyPattern ( '/^\|switch\|p2a: .*\|([^\|]+)\|.*$/mU', 'switchP2' );
        /*
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon1 = $matches[1];
        
        preg_match('/^\|switch\|p2a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon2 = $matches[1];
        */
        /* Turns. */
        $this->applyPattern ( '/\|turn\|([0-9]+)\n(.*)(?=\|turn\|)/sU', 'turnPreg' );
        
        // Last turn.
        $this->turns[] = $this->ProcessedText;

        /* Win. */
        $this->applyPattern ( '/\|win\|(.*)/', 'winnerPreg' );

        /* Tie. */
        $this->applyPattern ( '/\|tie\|/', 'tiePreg' );
        
        $this->Game->check ();
        
        $this->Game->save ();
//         if ( NULL == $this->Rules       ) throw new Exception ( 'Rules not provided'        );
//         if ( NULL == $this->TeamPreview ) throw new Exception ( 'Team Preview not provided' );
//         if ( NULL == $this->Winner      ) throw new Exception ( 'Winner not provided'       );
//         if ( NULL == $this->Tie         ) throw new Exception ( 'Tie not provided'          );
        Log::fct_exit ( __METHOD__ );
    }
    
    public function __toString ()
    {
        $String = '';
        
        $String .= $this->Game . "<br>\n";
        return $String;
    }
    
    public function display ()
    {
        /* ******************************** Traces. ******************************** */
        
//         var_dump($this->Player1);
//         var_dump($this->Player2);
        
//         var_dump($this->Team1);
//         var_dump($this->Team2);
        
//         var_dump($this->GameType);
        
//         var_dump($this->Gen);
        
//         var_dump($this->Tier);
        
//         var_dump($this->Rated);
        
//         var_dump($this->rules);
        
//         var_dump($this->teamPreview);
        
//         var_dump($this->winner);
        
//         var_dump($this->tie);
    }
    
    /* ************************************************************************* */

    /* Preg functions. */

    /**
     *  @brief Handles the description of a Player
     *  @param [in] $Match The list of arguments read from the text
     */
    protected function gameIdPreg ( $Match )
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $GameID = Arrays::getOrCrash ( $Match, 1, 'Invalid game ID' );

        Log::info ( "Parsing game $GameID" );
        
        $this->Game->setID ( $GameID );
        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     *  @brief Handles the description of a Player
     *  @param [in] $Match The list of arguments read from the text
     */
    protected function playerPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $PlayerPosition = Arrays::getOrCrash ( $Match, 1, 'Invalid player position' );
        $Username       = Arrays::getOrCrash ( $Match, 2, 'Invalid player name'     );
        $Avatar         = Arrays::getIfSet   ( $Match, 3, ''  );
        $Rating         = Arrays::getIfSet   ( $Match, 4, '0' );
        
        $this->Game->setPlayer ( new CollPlayer ( $PlayerPosition, $Username, $Avatar, $Rating ) );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     *  @brief Handles the description of a Team
     *  @param [in] $Match The list of arguments read from the Text
     */
    protected function teamSizePreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Player = Arrays::getOrCrash ( $Match, 1, 'Team not affected to as player' );
        $Size = Arrays::getOrCrash ( $Match, 2, 'Team quantity invalid' );
        
        $this->Game->setTeam ( new CollTeam ( $Player, $Size ) );
        Log::fct_exit ( __METHOD__ );
    }

    protected function gameTypePreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $GameType = Arrays::getOrCrash ( $Match, 1, 'Invalid Game Type' );
        $this->Game->setType ( $GameType );
        Log::fct_exit ( __METHOD__ );
    }

    protected function genPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Gen = Arrays::getOrCrash ( $Match, 1, 'Invalid Generation' );
        $this->Game->setGen ( $Gen );
        Log::fct_exit ( __METHOD__ );
    }

    protected function tierPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Tier = Arrays::getOrCrash ( $Match, 1, 'Invalid Tier' );
        $this->Game->setTier ( $Tier );
        Log::fct_exit ( __METHOD__ );
    }

    protected function ratedPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Rated   = Arrays::getOrCrash ( $Match, 1, 'Rated not filled' );
        $Message = Arrays::getIfSet   ( $Match, 2, '' );
        $this->Game->setRated ( $Message );
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function switchP1 ( $Match )
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 1, $Pokemon );
        Log::fct_exit ( __METHOD__ );
    }

    protected function switchP2 ( $Match )
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 2, $Pokemon );
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function rulePreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'rule');
    }

    protected function teamPreviewPokemonPreg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'teamPreviewPokemon');
    }

    protected function turnPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $this->Game->addTurn ( );
        Log::fct_exit ( __METHOD__ );
    }

    protected function move1Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move1');
    }

    protected function move2Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move2');
    }

    protected function winnerPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $User = Arrays::getOrCrash ( $Match, 1, 'User not filled' );
        $this->Game->setWinner ( $User );
        Log::fct_exit ( __METHOD__ );
    }

    protected function tiePreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $this->Game->setTie ( );
        Log::fct_exit ( __METHOD__ );
    }

    /* ************************************************************************* */

    /* Wrapper functions. */

    protected function move1 ( $pokemon, $move, $effect ) 
    {
//         return $this->move('1', $pokemon, $move, $effect);
    }

    protected function move2 ( $pokemon, $move, $effect ) 
    {
//         return $this->move('2', $pokemon, $move, $effect);
    }
}

// Log::setDebug ( __FILE__ );
