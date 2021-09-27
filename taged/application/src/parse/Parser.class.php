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
    private $Player1;
    private $Player2;
    private $Team1;
    private $Team2;
    private $GameType;
    private $Gen;
    private $Rated;
    private $Rules;
    private $TeamPreview;
    private $Winner;
    private $Tie;
    
    public function __construct ( $TextToParse ) 
    {
        $this->FullText = $TextToParse;
        
        $this->Player1 = NULL;
        $this->Player2 = NULL;
        $this->Team1 = NULL;
        $this->Team2 = NULL;
        $this->GameType = NULL;
        $this->Gen = NULL;
        $this->Rules = NULL;
        $this->TeamPreview = NULL;
        $this->Winner = NULL;
        
        $this->tie ( false );

        $this->clean ();
    }
    
    public function __destruct ( )
    {
        unset ( $this->Player1 );
        unset ( $this->Player2 );
        unset ( $this->Team1 );
        unset ( $this->Team2 );
        unset ( $this->Gen );
        unset ( $this->Rules );
        unset ( $this->TeamPreview );
        unset ( $this->Winner );
        unset ( $this->Rated );
        unset ( $this->Tie );
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
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon1 = $matches[1];
        
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon2 = $matches[1];

        /* Turns. */
        $this->applyPattern ( '/\|turn\|([0-9]+)\n(.*)(?=\|turn\|)/sU', 'turnPreg' );
        
        // Last turn.
        $this->turns[] = $this->ProcessedText;

        /* Win. */
        $this->applyPattern ( '/\|win\|(.*)/', 'winnerPreg' );

        /* Tie. */
        $this->applyPattern ( '/\|tie\|/', 'tiePreg' );
        
        if ( NULL == $this->Player1     ) throw new Exception ( 'Player 1 not provided'     );
        if ( NULL == $this->Player2     ) throw new Exception ( 'Player 2 not provided'     );
        if ( NULL == $this->Team1       ) throw new Exception ( 'Team 1 not provided'       );
        if ( NULL == $this->Team2       ) throw new Exception ( 'Team 2 not provided'       );
        if ( NULL == $this->GameType    ) throw new Exception ( 'Game Type not provided'    );
        if ( NULL == $this->Gen         ) throw new Exception ( 'Generation not provided'   );
        if ( NULL == $this->Tier        ) throw new Exception ( 'Tier not provided'         );
        if ( NULL == $this->Rated       ) $this->Rated = new Rated ( false );
//         if ( NULL == $this->Rules       ) throw new Exception ( 'Rules not provided'        );
//         if ( NULL == $this->TeamPreview ) throw new Exception ( 'Team Preview not provided' );
//         if ( NULL == $this->Winner      ) throw new Exception ( 'Winner not provided'       );
//         if ( NULL == $this->Tie         ) throw new Exception ( 'Tie not provided'          );
    }
    
    public function __toString ()
    {
        $String = '';
        
        $String .= $this->Player1 . "<br>\n";
        $String .= $this->Team1 . "<br>\n";
        $String .= $this->Player2 . "<br>\n";
        $String .= $this->Team2 . "<br>\n";
        $String .= $this->GameType . "<br>\n";
        $String .= $this->Gen . "<br>\n";
        $String .= $this->Tier . "<br>\n";
        $String .= $this->Rated . "<br>\n";
//         $String .= 'Rules ' . $this->Rules . "<br>\n";
//         $String .= 'Team Preview ' . $this->TeamPreview . "<br>\n";
//         $String .= 'Winner ' . $this->Winner . "<br>\n";
//         $String .= 'Tie ' . $this->Tie . "<br>\n";
        return $String;
    }
    
    public function display ()
    {
        /* ******************************** Traces. ******************************** */
        
        var_dump($this->Player1);
        var_dump($this->Player2);
        
        var_dump($this->Team1);
        var_dump($this->Team2);
        
        var_dump($this->GameType);
        
        var_dump($this->Gen);
        
        var_dump($this->Tier);
        
        var_dump($this->Rated);
        
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
        
        if ( 1 == $Player->getPlayer () ) 
        {
            $this->Player1 = $Player;
        } 
        else
        {
            $this->Player2 = $Player;
        }
    }

    /**
     *  @brief Handles the description of a Team
     *  @param [in] $Match The list of arguments read from the Text
     */
    protected function teamSizePreg ( $Match ) 
    {
        $TeamSize = TeamSize::create ( $Match );

        if ( 1 == $TeamSize->getPlayer () ) 
        {
            $this->Team1 = $TeamSize;
        } 
        else 
        {
            $this->Team2 = $TeamSize;
        }
    }

    protected function gameTypePreg ( $Match ) 
    {
        $this->GameType = GameType::create ( $Match );
    }

    protected function genPreg ( $Match ) 
    {
        $this->Gen = Gen::create ( $Match );
    }

    protected function tierPreg ( $Match ) 
    {
        $this->Tier = Tier::create ( $Match );
    }

    protected function ratedPreg ( $Match ) 
    {
        $this->Rated = Rated::create ( $Match );
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
//         return $this->matchFunc1($match, 'winner');
    }

    protected function tiePreg ( $Match ) 
    {
//         return $this->matchFunc0($match, 'tie');
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

    /* ************************************************************************* */

    /* Model functions. */

//     function teamSize($player, $number) {
//         require_once 'TeamSize.php';

//     }

//     function gameType($gameType) {
//         require_once 'GameType.php';

//         $this->gameType = new GameType($gameType);
//     }

//     function gen($gen) {
//         require_once 'Gen.php';

//         $this->gen = new Gen($gen);
//     }

//     function tier($tier) {
//         require_once 'Tier.php';

//         $this->tier = new Tier($tier);
//     }

//     function rated ( $rated, $message = '' ) 
//     {
//         require_once 'Rated.php';

//         if ($rated == 'rated') {
//             $this->rated = new Rated(true, $message);
//         }
//         $this->rated = new Rated(false, $message);
//     }

//     function rule($rule) {
//         require_once 'Rule.php';

//         $this->rules[] = new Rule($rule);
//     }

//     function teamPreviewPokemon($player, $details, $item) {
//         require_once 'TeamPreviewPokemon.php';

//         $this->teamPreview[] = new TeamPreviewPokemon($player, $details, $item);
//     }

//     function turn($urnNum, $turn) {
//         require_once 'Turn.php';

//         $this->turns[$urnNum] = new Turn($turn);
//         $this->currentTurn = $this->turns[$urnNum];

//         // TODO : faint can be a kind of move.

//         $turn = preg_replace_callback('/\|move\|p1a: ([^\|]+)\|([^\|]+)\|.*(?=\|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))/sU', array($this, 'move1Preg'), $turn);
//         $turn = preg_replace_callback('/\|move\|p2a: ([^\|]+)\|([^\|]+)\|.*(?=\|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))/sU', array($this, 'move2Preg'), $turn);

//         return '';
//     }

//     function move($player, $pokemon, $move, $effect) {
//         require_once 'Move.php';

//         $memberFunction = 'setP' . $player . 'Move';

//         $this->currentTurn->{$memberFunction}(new Move($player, $pokemon, $move, $effect));

//         // TODO : sidestart, sideend

//         // TODO : ([^\\|]+)\|([0-9]+(?=\\\/)|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))\|-([^\\|]+)\|p([12])[^ ]+ ([^\\|]+)\|([0-9]+(?=\\\/)|)

//         return '';
//     }

//     function effect() {
//         $effect = '';

//         switch($effect) {
//             case 'sidestart';
//                 break;
//             case 'sideend';
//                 break;
//             case 'damage';
//                 break;
//             case 'boost';
//                 break;
//             case 'mega';
//                 break;
//             case 'supereffective';
//                 break;
//             case 'status';
//                 break;
//         }
//     }

    protected function winner ( $user ) 
    {
//         require_once 'Winner.php';

//         $this->winner = new Winner($user);
    }

    protected function tie ( $tie = false ) 
    {
//         $this->tie = $tie;
    }
}