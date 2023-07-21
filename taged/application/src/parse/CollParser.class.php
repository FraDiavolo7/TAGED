<?php

/**
 * Parser Collection ( Pokemon Showdown )
 * @package TAGED\Parser\Collection
 */
class CollParser 
{
    
    /**
     * Construit un nouvel objet CollParser.
     * 
     * @param string $TextToParse Le texte brut à analyser.
     */
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
     * Nettoie le contenu de $this->FullText de tous les caractères indésirables.
     * Utilise le modificateur d'expression régulière m (PCRE_MULTILINE) pour utiliser des caractères "\n" dans une chaîne de sujet afin de faire correspondre des groupes multilignes (particulièrement utile pour les listes).
     */
    private function clean () 
    {
        $tmp = preg_replace('/\R/u', "\n", $this->FullText);
        
        preg_replace_callback ( '/input type="hidden" name="replayid" value="([^"]*)" \//m', array ( $this, "gameIdPreg" ), $tmp );
        
        $this->ProcessedText  = preg_replace('/^.*\<script type="text\/plain" class="battle-log-data"\>(.*)\<\/script\>.*$/sU', '$1', $tmp);
    }

    /**
     * Applique un motif (pattern) avec une fonction de rappel sur le texte traité.
     * 
     * @param string $Pattern Le motif (pattern) d'expression régulière à appliquer.
     * @param string $Callback La fonction de rappel à appeler pour chaque correspondance.
     * @param int $Count Le nombre de fois que le motif doit être appliqué (facultatif, par défaut 1).
     */
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
     * Analyse le texte traité (ProcessedText) avec des expressions régulières (PCRE).
     * 
     * Cette méthode parcourt le texte et utilise des motifs d'expression régulière pour extraire des informations spécifiques.
     * Elle appelle les fonctions de rappel appropriées pour traiter les correspondances trouvées.
     * 
     * @throws Exception En cas de problème lors de l'analyse.
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
        //$this->applyPattern ( '/\|turn\|([0-9]+)\n(.*)(?=\|turn\|)/sU', 'turnPreg' );
        $this->applyPattern ( '/\|turn\|([0-9]+)\n/sU', 'turnPreg' );
        
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
    
    /**
     * Convertit l'objet CollParser en une chaîne de caractères pour l'affichage.
     * 
     * @return string La représentation de l'objet sous forme de chaîne de caractères.
     */
    public function __toString ()
    {
        $String = '';
        
        $String .= $this->Game . "<br>\n";
        return $String;
    }
    
    /**
     * Affiche les informations analysées pour le débogage.
     * 
     * Cette méthode affiche les différentes propriétés et informations analysées par la classe pour le débogage.
     * Elle permet de vérifier que les données ont été correctement extraites et organisées.
     */
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
     * Gère la description d'un Pokémon dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
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
     * Gère la description d'un joueur (Player) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
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
     * Gère la description d'une équipe (Team) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
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

    /**
     * Gère la description du type de jeu (Game Type) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function gameTypePreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $GameType = Arrays::getOrCrash ( $Match, 1, 'Invalid Game Type' );
        $this->Game->setType ( $GameType );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     * Gère la description de la génération (Gen) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function genPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Gen = Arrays::getOrCrash ( $Match, 1, 'Invalid Generation' );
        $this->Game->setGen ( $Gen );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     * Gère la description du tier dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function tierPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Tier = Arrays::getOrCrash ( $Match, 1, 'Invalid Tier' );
        $this->Game->setTier ( $Tier );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     * Gère la description d'une partie notée (Rated) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function ratedPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Rated   = Arrays::getOrCrash ( $Match, 1, 'Rated not filled' );
        $Message = Arrays::getIfSet   ( $Match, 2, '' );
        $this->Game->setRated ( $Message );
        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     * Gère la description d'un switch du joueur 1 (P1) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function switchP1 ( $Match )
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 1, $Pokemon );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     * Gère la description d'un switch du joueur 2 (P2) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function switchP2 ( $Match )
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $Pokemon = Arrays::getOrCrash ( $Match, 1, 'Pokemon not filled' );
        $this->Game->switch ( 2, $Pokemon );
        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     * Gère la description d'une règle (Rule) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
	 * @deprecated
     */
    protected function rulePreg ( $Match ) 
    {
//         return $this->matchFunc1($match, 'rule');
    }

    /**
     * Gère la description d'un Pokémon lors du choix des équipes (Team Preview) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
	 * @deprecated
     */
    protected function teamPreviewPokemonPreg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'teamPreviewPokemon');
    }

    /**
     * Gère la description d'un tour (Turn) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function turnPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $this->Game->addTurn ( );
        Log::fct_exit ( __METHOD__ );
    }

    /**
     * Gère la description d'un mouvement (Move) lors d'un switch dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function move1Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move1');
    }

    /**
     * Gère la description d'un mouvement (Move) lors d'un switch dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function move2Preg ( $Match ) 
    {
//         return $this->matchFunc3($match, 'move2');
    }

    /**
     * Gère la description du gagnant (Winner) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
    protected function winnerPreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $User = Arrays::getOrCrash ( $Match, 1, 'User not filled' );
        $this->Game->setWinner ( $User );
        Log::fct_exit ( __METHOD__ );
    }

     /**
     * Gère la description d'une égalité (Tie) dans le texte traité.
     * 
     * @param array $Match Le tableau des arguments lus depuis le texte.
     */
   protected function tiePreg ( $Match ) 
    {
        Log::fct_enter ( __METHOD__ );
        Log::info ( json_encode ( $Match ) );
        $this->Game->setTie ( );
        Log::fct_exit ( __METHOD__ );
    }

    /* ************************************************************************* */

    /* Wrapper functions. */

    /**
     * Gère la description d'un mouvement (Move) d'un Pokémon dans le texte traité.
     * 
     * @param string $pokemon Le nom du Pokémon.
     * @param string $move Le nom du mouvement (Move).
     * @param string $effect L'effet du mouvement (Move).
     */
    protected function move1 ( $pokemon, $move, $effect ) 
    {
//         return $this->move('1', $pokemon, $move, $effect);
    }

    /**
     * Gère la description d'un mouvement (Move) d'un Pokémon dans le texte traité.
     * 
     * @param string $pokemon Le nom du Pokémon.
     * @param string $move Le nom du mouvement (Move).
     * @param string $effect L'effet du mouvement (Move).
     */
    protected function move2 ( $pokemon, $move, $effect ) 
    {
//         return $this->move('2', $pokemon, $move, $effect);
    }
	
	
	/**
     * @var int Le nombre de tours.
     */
    private $turns;

    /**
     * @var string Le texte brut complet à analyser.
     */
    private $FullText;

    /**
     * @var string Le texte traité après nettoyage.
     */
    private $ProcessedText;

    /**
     * @var CollGame L'objet CollGame associé aux données analysées.
     */
    private $Game;

}

// Log::setDebug ( __FILE__ );
