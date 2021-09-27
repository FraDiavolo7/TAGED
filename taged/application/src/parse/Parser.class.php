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
    private $gameType;
    private $gen;
    private $rated;
    private $rules;
    private $teamPreview;
    private $currentPokemon1;
    private $currentPokemon2;
    private $turns;
    private $currentTurn;
    private $winner;
    private $tie;
    
    private $FullText;
    private $ProcessedText;
    private $Player1;
    private $Player2;
    private $Team1;
    private $Team2;
    
    public function __construct ( $TextToParse ) 
    {
        $this->FullText = $TextToParse;
        $this->Rated ( false );
        $this->Tie ( false );

        $this->clean ();
    }

    /**
     *  @brief Clean the $this->FullText member variable of all undesirables characters.
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
//         for ( $i = 0 ; $i < $Count ; ++$i )
//         {
            $this->ProcessedText = preg_replace_callback ( $Pattern, array ( $this, $Callback ), $this->ProcessedText );
//         }
    }

    /**
     *  @brief Parse (with PCRE) the $this->text member variable.
     */
    public function parse () 
    {
        /* ******************************** Battle initialization. ******************************** */
        /* Players. */
        $this->applyPattern ( '/\|player\|p([0-9]+)\|([^\|\n|\r|\f]+)\|([^\|\n|\r|\f]+)(?:\|([^\|\n|\r|\f]+))?(?:\|([^\|\n|\r|\f]+))?(?:\|)?/', 'PlayerPreg' );

        /* Teams sizes. */
        $this->applyPattern ( '/\|teamsize\|p([0-9]+)\|([0-9])/', 'TeamSizePreg' );

        /* Game type. */
        $this->applyPattern ( '/\|gametype\|(.*)/', 'GameTypePreg' );

        /* Gen. */
        $this->applyPattern ( '/\|gen\|(.*)/', 'GenPreg' );

        /* Tier. */
        $this->applyPattern ( '/\|tier\|(.*)/', 'TierPreg' );

        /* Rated. */
        $this->applyPattern ( '/\|(rated)\|(.*)/', 'RatedPreg' );

        /* Rule(s). */
        $NbRules = substr_count ( $this->ProcessedText, '|rule|' );
        $this->applyPattern ( '/\|rule\|(.*)/', 'RulePreg', $NbRules );
        
        /* Team preview(s). */
        $NbPreview = substr_count ( $this->ProcessedText, '|poke|' );
        $this->applyPattern ( '/\|poke\|(.*)\|(.*)\|(.*)/', 'TeamPreviewPokemonPreg', $NbPreview );

        /* ******************************** Battle progress. ******************************** */

        $this->ProcessedText = preg_replace('/\|start[\|\n|\r|\f](.*)$/sU', '$1', $this->ProcessedText);

        /* First (current) pokemons. */
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon1 = $matches[1];
        
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->ProcessedText, $matches);
        $this->currentPokemon2 = $matches[1];

        /* Turns. */
        $this->applyPattern ( '/\|turn\|([0-9]+)\n(.*)(?=\|turn\|)/sU', 'TurnPreg' );
        
        // Last turn.
        $this->turns[] = $this->ProcessedText;

        /* Win. */
        $this->applyPattern ( '/\|win\|(.*)/', 'WinnerPreg' );

        /* Tie. */
        $this->applyPattern ( '/\|tie\|/', 'TiePreg' );

        /* ******************************** Traces. ******************************** */

        var_dump($this->Player1);
        var_dump($this->Player2);

        var_dump($this->Team1);
        var_dump($this->Team2);

        var_dump($this->gameType);

        var_dump($this->gen);

        var_dump($this->tier);

        var_dump($this->rated);

        var_dump($this->rules);

        var_dump($this->teamPreview);

        var_dump($this->winner);

        var_dump($this->tie);
    }

    /* ************************************************************************* */

    /* Preg functions. */

    /**
     *  @brief Handles the description of a Player
     *  @param [in] $Match The list of arguments read from the text
     */
    protected function PlayerPreg ( $Match ) 
    {
        echo 'PlayerPreg ( ' . print_r ( $Match, true ) . ' )<br>';
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
    protected function TeamSizePreg ( $Match ) 
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

    protected function GameTypePreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'gameType');
    }

    protected function GenPreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'gen');
    }

    protected function TierPreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'tier');
    }

    protected function RatedPreg ( $Match ) 
    {
//         return $this->matchFunc2($match, 'rated');
    }

    protected function RulePreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'rule');
    }

    protected function TeamPreviewPokemonPreg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'teamPreviewPokemon');
    }

    protected function TurnPreg ( $Match ) 
    {
//         return $this->matchFunc2($match, 'turn');
    }

    protected function Move1Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move1');
    }

    protected function Move2Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move2');
    }

    protected function WinnerPreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'winner');
    }

    protected function TiePreg ( $Match ) 
    {
//         return $this->matchFunc0($match, 'tie');
    }

    /* ************************************************************************* */

    /* Wrapper functions. */

    protected function Move1 ( $pokemon, $move, $effect ) 
    {
//         return $this->move('1', $pokemon, $move, $effect);
    }

    protected function Move2 ( $pokemon, $move, $effect ) 
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

    function Rated ( $rated, $message = '' ) 
    {
//         require_once 'Rated.php';

//         if ($rated == 'rated') {
//             $this->rated = new Rated(true, $message);
//         }
//         $this->rated = new Rated(false, $message);
    }

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

    protected function Winner ( $user ) 
    {
//         require_once 'Winner.php';

//         $this->winner = new Winner($user);
    }

    protected function Tie ( $tie = false ) 
    {
//         $this->tie = $tie;
    }
}