<?php
/*
 * Copyright © 2013 Diveen
 * All Rights Reserved.
 *
 * This software is proprietary and confidential to Diveen ReplayParser
 * and is protected by copyright law as an unpublished work.
 *
 * Unauthorized access and disclosure strictly forbidden.
 */

/**
 * @author Mickaël Martin-Nevot
 */

class Parser {
    const REPLAYS_PATH = 'replays/';

    private $filename;
    private $head;
    private $text;
    private $currentPokemon1;
    private $currentPokemon2;
    private $currentTurn;
    private $turns;
    
    private $FullText;
    private $ProcessedText;
    private $Game;
    
    public function __construct ( $TextToParse ) 
    {
        $this->FullText = $TextToParse;
        
        $this->Game = new Game ();
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
        $this->applyPattern ( '/^\|switch\|p1a: ([^\|]+)\|.*$/mU', 'switchP1' );
        $this->applyPattern ( '/^\|switch\|p2a: ([^\|]+)\|.*$/mU', 'switchP2' );
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
//         if ( NULL == $this->Rules       ) throw new Exception ( 'Rules not provided'        );
//         if ( NULL == $this->TeamPreview ) throw new Exception ( 'Team Preview not provided' );
//         if ( NULL == $this->Winner      ) throw new Exception ( 'Winner not provided'       );
//         if ( NULL == $this->Tie         ) throw new Exception ( 'Tie not provided'          );
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
    protected function playerPreg ( $Match ) 
    {
        //         echo 'PlayerPreg ( ' . print_r ( $Match, true ) . ' )<br>';
        $Player = Player::create ( $Match );
        $this->Game->setPlayer ( $Player );
    }

    /**
     *  @brief Handles the description of a Team
     *  @param [in] $Match The list of arguments read from the Text
     */
    protected function teamSizePreg ( $Match ) 
    {
        $Team = Team::create ( $Match );
        $this->Game->setTeam ( $Team );
    }

    protected function gameTypePreg ( $Match ) 
    {
        $GameType = Arrays::getOrCrash ( $Match, 1, 'Invalid Game Type' );
        $this->Game->setType ( $GameType );
    }

    protected function genPreg ( $Match ) 
    {
        $Gen = Arrays::getOrCrash ( $Match, 1, 'Invalid Generation' );
        $this->Game->setGen ( $Gen );
    }

    protected function tierPreg ( $Match ) 
    {
        $Tier = Arrays::getOrCrash ( $Match, 1, 'Invalid Tier' );
        $this->Game->setTier ( $Tier );
    }

    protected function ratedPreg ( $Match ) 
    {
        $Rated   = Arrays::getOrCrash ( $Match, 1, 'Rated not filled' );
        $Message = Arrays::getIfSet   ( $Match, 2, '' );
        $this->Game->setRated ( $Message );
    }
    
    protected function switchP1 ( $Match )
    {
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 1, $Pokemon );
    }

    protected function switchP2 ( $Match )
    {
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 2, $Pokemon );
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
//         return $this->matchFunc2($match, 'turn');
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
        $User = Arrays::getOrCrash ( $Match, 1, 'User not filled' );
        $this->Game->setWinner ( $User );
    }

    protected function tiePreg ( $Match ) 
    {
        $this->Game->setTie ( );
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