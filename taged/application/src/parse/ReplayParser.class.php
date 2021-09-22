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

class ReplayParser {
    const REPLAYS_PATH = 'replays/';

    private $filename;
    private $head;
    private $text;
    private $player1;
    private $player2;
    private $team1;
    private $team2;
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

    public function __construct($name) {
        $this->filename = self::REPLAYS_PATH . $name . '.html';

        /* Get file content into $this->text member variable. */
        $this->getPage();

        /* Clean the $this->text member variable of all undesirables characters. */
        $this->cleaner();

        /* Parse (with PCRE) the $this->text member variable. */
        $this->parser();
    }

    private function getPage() {
        if (file_exists($this->filename)) {
            $this->text = file_get_contents($this->filename);
        } else {
            echo 'Replay loading error.';
        }
    }

    private function cleaner() {
        // Use because PCRE pattern modifier m (PCRE_MULTILINE) have to use "\n" characters in a subject string to match
        // multiline group (particularly useful for lists).
        $text = preg_replace('/\R/u', "\n", $this->text);
    }

    private function parser() {
        $this->text  = preg_replace('/^.*\<script type="text\/plain" class="battle-log-data"\>(.*)\<\/script\>.*$/sU', '$1', $this->text);

        //echo $this->text;

        /* ******************************** Battle initialization. ******************************** */

        /* Players. */
        $this->text = preg_replace_callback('/\|player\|p([0-9]+)\|([^\|\n|\r|\f]+)\|([^\|\n|\r|\f]+)(?:\|([^\|\n|\r|\f]+))?(?:\|([^\|\n|\r|\f]+))?(?:\|)?/', array($this, 'playerPreg'), $this->text);

        /* Teams sizes. */
        $this->text = preg_replace_callback('/\|teamsize\|p([0-9]+)\|([0-9])/', array($this, 'teamSizePreg'), $this->text);

        /* Game type. */
        $this->text = preg_replace_callback('/\|gametype\|(.*)/', array($this, 'gameTypePreg'), $this->text);

        /* Gen. */
        $this->text = preg_replace_callback('/\|gen\|(.*)/', array($this, 'genPreg'), $this->text);

        /* Tier. */
        $this->text = preg_replace_callback('/\|tier\|(.*)/', array($this, 'tierPreg'), $this->text);

        /* Rated. */
        $this->rated(false);
        $this->text = preg_replace_callback('/\|(rated)\|(.*)/', array($this, 'ratedPreg'), $this->text);

        /* Rule(s). */
        $iMax = substr_count($this->text, '|rule|');
        for ($i = 0 ; $i < $iMax ; ++$i) {
            $this->text = preg_replace_callback('/\|rule\|(.*)/', array($this, 'rulePreg'), $this->text);
        }

        /* Team preview(s). */
        $iMax = substr_count($this->text, '|poke|');
        for ($i = 0 ; $i < $iMax ; ++$i) {
            $this->text = preg_replace_callback('/\|poke\|(.*)\|(.*)\|(.*)/', array($this, 'teamPreviewPokemonPreg'), $this->text);
        }

        /* ******************************** Battle progress. ******************************** */

        $this->text = preg_replace('/\|start[\|\n|\r|\f](.*)$/sU', '$1', $this->text);

        /* First (current) pokemons. */
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->text, $matches);
        $this->currentPokemon1 = $matches[1];
        preg_match('/^\|switch\|p1a: ([^\|]+)\|.*$/mU', $this->text, $matches);
        $this->currentPokemon2 = $matches[1];

        /* Turns. */
        $this->text = preg_replace_callback('/\|turn\|([0-9]+)\n(.*)(?=\|turn\|)/sU', array($this, 'turnPreg'), $this->text);
        // Last turn.
        $this->turns[] = $this->text;

        /* Win. */
        $this->text = preg_replace_callback('/\|win\|(.*)/', array($this, 'winnerPreg'), $this->text);

        /* Tie. */
        $this->tie(false);
        $this->text = preg_replace_callback('/\|tie\/', array($this, 'tiePreg'), $this->text);

        /* ******************************** Traces. ******************************** */

        var_dump($this->player1);
        var_dump($this->player2);

        var_dump($this->team1);
        var_dump($this->team2);

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

    /* Match functions. */

    function matchFunc0($match, $callback) {
        if(isset($match[0])) {
            return $this->{$callback}();
        }
    }

    function matchFunc1($match, $callback) {
        if(isset($match[0], $match[1])) {
            return $this->{$callback}($match[1]);
        }
    }

    function matchFunc2($match, $callback) {
        if(isset($match[0], $match[1])) {
            if (isset($match[2])) {
                return $this->{$callback}($match[1], $match[2]);
            } else {
                return $this->{$callback}($match[1]);
            }
        }
    }

    function matchFunc3($match, $callback) {
        if(isset($match[0], $match[1])) {
            if (isset($match[2])) {
                if (isset($match[3])) {
                    return $this->{$callback}($match[1], $match[2], $match[3]);
                }
                return $this->{$callback}($match[1], $match[2]);
            }
            return $this->{$callback}($match[1]);
        }
    }

    function matchFunc4($match, $callback) {
        if(isset($match[0], $match[1])) {
            if (isset($match[2])) {
                if (isset($match[3])) {
                    if (isset($match[4])) {
                        return $this->{$callback}($match[1], $match[2], $match[3], $match[4]);
                    }
                    return $this->{$callback}($match[1], $match[2], $match[3]);
                }
                return $this->{$callback}($match[1], $match[2]);
            }
            return $this->{$callback}($match[1]);
        }
    }

    function matchFunc5($match, $callback) {
        if(isset($match[0], $match[1])) {
            if (isset($match[2])) {
                if (isset($match[3])) {
                    if (isset($match[4])) {
                        if (isset($match[5])) {
                            return $this->{$callback}($match[1], $match[2], $match[3], $match[4], $match[5]);
                        }
                        return $this->{$callback}($match[1], $match[2], $match[3], $match[4]);
                    }
                    return $this->{$callback}($match[1], $match[2], $match[3]);
                }
                return $this->{$callback}($match[1], $match[2]);
            }
            return $this->{$callback}($match[1]);
        }
    }

    function matchFunc6($match, $callback) {
        if(isset($match[0], $match[1])) {
            if (isset($match[2])) {
                if (isset($match[3])) {
                    if (isset($match[4])) {
                        if (isset($match[5])) {
                            if (isset($match[6])) {
                                return $this->{$callback}($match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
                            }
                            return $this->{$callback}($match[1], $match[2], $match[3], $match[4], $match[5]);
                        }
                        return $this->{$callback}($match[1], $match[2], $match[3], $match[4]);
                    }
                    return $this->{$callback}($match[1], $match[2], $match[3]);
                }
                return $this->{$callback}($match[1], $match[2]);
            }
            return $this->{$callback}($match[1]);
        }
    }

    /* ************************************************************************* */

    /* Preg functions. */

    function playerPreg($match) {
        return $this->matchFunc4($match, 'player');
    }

    function teamSizePreg($match) {
        return $this->matchFunc2($match, 'teamSize');
    }

    function gameTypePreg($match) {
        return $this->matchFunc1($match, 'gameType');
    }

    function genPreg($match) {
        return $this->matchFunc1($match, 'gen');
    }

    function tierPreg($match) {
        return $this->matchFunc1($match, 'tier');
    }

    function ratedPreg($match) {
        return $this->matchFunc2($match, 'rated');
    }

    function rulePreg($match) {
        return $this->matchFunc1($match, 'rule');
    }

    function teamPreviewPokemonPreg($match) {
        return $this->matchFunc3($match, 'teamPreviewPokemon');
    }

    function turnPreg($match) {
        return $this->matchFunc2($match, 'turn');
    }

    function move1Preg($match) {
        return $this->matchFunc3($match, 'move1');
    }

    function move2Preg($match) {
        return $this->matchFunc3($match, 'move2');
    }

    function winnerPreg($match) {
        return $this->matchFunc1($match, 'winner');
    }

    function tiePreg($match) {
        return $this->matchFunc0($match, 'tie');
    }

    /* ************************************************************************* */

    /* Wrapper functions. */

    function move1($pokemon, $move, $effect) {
        return $this->move('1', $pokemon, $move, $effect);
    }

    function move2($pokemon, $move, $effect) {
        return $this->move('2', $pokemon, $move, $effect);
    }

    /* ************************************************************************* */

    /* Model functions. */

    function player($player, $username, $avatar, $rating = null) {
        require_once 'Player.php';

        //echo 'player() : $', $player, '$ #', $username, '# ¤', $avatar, '¤ ~', $rating, '~', PHP_EOL;

        if (1 == $player) {
            $this->player1 = new Player($player, $username, $avatar, $rating);
        } else if (2 == $player) {
            $this->player2 = new Player($player, $username, $avatar, $rating);
        }
    }

    function teamSize($player, $number) {
        require_once 'TeamSize.php';

        if (1 == $player) {
            $this->team1 = new TeamSize($player, $number);
        } else if (2 == $player) {
            $this->team2 = new TeamSize($player, $number);
        }
    }

    function gameType($gameType) {
        require_once 'GameType.php';

        $this->gameType = new GameType($gameType);
    }

    function gen($gen) {
        require_once 'Gen.php';

        $this->gen = new Gen($gen);
    }

    function tier($tier) {
        require_once 'Tier.php';

        $this->tier = new Tier($tier);
    }

    function rated($rated, $message = '') {
        require_once 'Rated.php';

        if ($rated == 'rated') {
            $this->rated = new Rated(true, $message);
        }
        $this->rated = new Rated(false, $message);
    }

    function rule($rule) {
        require_once 'Rule.php';

        $this->rules[] = new Rule($rule);
    }

    function teamPreviewPokemon($player, $details, $item) {
        require_once 'TeamPreviewPokemon.php';

        $this->teamPreview[] = new TeamPreviewPokemon($player, $details, $item);
    }

    function turn($urnNum, $turn) {
        require_once 'Turn.php';

        $this->turns[$urnNum] = new Turn($turn);
        $this->currentTurn = $this->turns[$urnNum];

        // TODO : faint can be a kind of move.

        $turn = preg_replace_callback('/\|move\|p1a: ([^\|]+)\|([^\|]+)\|.*(?=\|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))/sU', array($this, 'move1Preg'), $turn);
        $turn = preg_replace_callback('/\|move\|p2a: ([^\|]+)\|([^\|]+)\|.*(?=\|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))/sU', array($this, 'move2Preg'), $turn);

        return '';
    }

    function move($player, $pokemon, $move, $effect) {
        require_once 'Move.php';

        $memberFunction = 'setP' . $player . 'Move';

        $this->currentTurn->{$memberFunction}(new Move($player, $pokemon, $move, $effect));

        // TODO : sidestart, sideend

        // TODO : ([^\\|]+)\|([0-9]+(?=\\\/)|)(.*)(?=(?=\n\|\n\|upkeep)|(?=\n\|move))\|-([^\\|]+)\|p([12])[^ ]+ ([^\\|]+)\|([0-9]+(?=\\\/)|)

        return '';
    }

    function effect() {
        $effect = '';

        switch($effect) {
            case 'sidestart';
                break;
            case 'sideend';
                break;
            case 'damage';
                break;
            case 'boost';
                break;
            case 'mega';
                break;
            case 'supereffective';
                break;
            case 'status';
                break;
        }
    }

    function winner($user) {
        require_once 'Winner.php';

        $this->winner = new Winner($user);
    }

    function tie($tie = false) {
        $this->tie = $tie;
    }
}