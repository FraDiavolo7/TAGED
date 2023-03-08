<?php

class CollTeam 
{
    const TABLE = 'equipe';
    const TABLE_ALIGNE = 'aligne';
    const TABLE_POKEMON = 'pokemon';
    
    const ID = 'id_equipe';
    const NOMBRE = 'nombre';
    const LISTE  = 'liste';
    const LISTE2 = 'liste2';
    const LISTE3 = 'liste3';
    const LISTE4 = 'liste4';
    const LISTE5 = 'liste5';
    const ORDRE = 'ordre';
    const NOM = 'nom';
    const GENERATION = 'generation';
    const RARETE = 'rarete';
    const DROP_RATE  = 'drop_rate';
    const DROP_RATE2 = 'drop_rate2';
    const DROP_RATE3 = 'drop_rate3';
    const DROP_RATE4 = 'drop_rate4';
    const DROP_RATE5 = 'drop_rate5';
    
    const POKEDEX = CONFIG_HOME . 'pokedex.csv';
    
    /**
     * @var $Player is p1, p2, p3, or p4.
     */
    private $Player;

    /**
     * @var $Size is the number of Pokémon your opponent starts with. In games without ReplayParser Preview, you don't know which Pokémon your opponent has, but you at least know how many there are.
     */
    private $Size;
    
    /**
     * @var $Pokemons is the array of Pokemons, index by position
     */
    private $Pokemons;
    private $DropRate;
    private $TeamDropRate;
    
    private $IDEquipe; // ID dans la table Equipe
    
    /**
     * TeamSize constructor.
     * @param int $Player The player position of the Team 
     * @param int $Size The Quantity of pokemons in the Team 
     */
    public function __construct ( $Player, $Size, $EquipeID = -1, $Pokemons = array (), $DropRate = 0 ) 
    {
        $this->Player = $Player;
        $this->Size = $Size;
        
        $this->IDEquipe = $EquipeID;
        $this->Pokemons = $Pokemons;
        $this->DropRate = array ();
        $this->TeamDropRate = $DropRate;
    }
    
    public function __toString ( )
    {
        $Result = 'Team ' . $this->Player . ' has ' . $this->Size . ' pokemon (';
        $Sep = '';
        foreach ( $this->Pokemons as $Pokemon )
        {
            $Result .= $Sep . $Pokemon;
            $Sep = ', ';
        }
        $Result .= ') [' . $this->TeamDropRate . ']';
        return $Result;
    }
    
    public function switch ( $Pokemon )
    {
         $this->addPokemon ( $Pokemon );
    }
    
    /**
     * @return The player position of the Team.
     */
    public function getPlayer ()
    {
        return $this->Player;
    }

    /**
     * @param int $Player The player position of the Team 
     */
    public function setPlayer ( $Player )
    {
        $this->Player = $Player;
    }

    /**
     * @return The drop rate of the Team.
     */
    public function getDropRate ()
    {
        return $this->TeamDropRate;
    }
    
    /**
     * @return The Quantity of pokemons in the Team.
     */
    public function getSize ()
    {
        return $this->Size;
    }

    /**
     * @return The ID of the Team.
     */
    public function getID ()
    {
        return $this->IDEquipe;
    }
    
    /**
     * 
     * @param integer $PokemonNum The number of the pokemon in the list
     * @return The Pokemon or le full list
     */
    public function getPokemon ( $PokemonNum = 0)
    {
        if ( $PokemonNum == 0 )
        {
            return implode ( ',', $this->Pokemons );
        }
        else
        {
            return $this->Pokemons [$PokemonNum];
        }
    }
    
    /**
     * @param int $Size The Quantity of pokemons in the Team 
     */
    public function setSize ( $Size )
    {
        $this->Size = $Size;
    }
    
    /**
     * Adds a Pokemon to the list
     * @param String $Pokemon The name of the pokemon
     */
    public function addPokemon ( $Pokemon )
    {
        $RealName = explode ( ',', $Pokemon ) [0];
        $this->Pokemons [ ] = $RealName;
    }

    protected function savePokemon ( $Pokemon )
    {
        Log::fct_enter ( __METHOD__ );
        $DropRate = 0;
        
        TagedDBColl::execute ( 'SELECT ' . self::NOM . ', ' . self::DROP_RATE . ' FROM ' . self::TABLE_POKEMON . " WHERE " . self::NOM . " = '" . $Pokemon ."'" );
        $Results = TagedDBColl::getResults ( );

        $DropRate   = Arrays::getIfSet ( $Results, 1, 0 );
        
        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entr�e Pokemon
            // Récupération des données statiques
            
            $StaticData = Arrays::getCSVLine ( self::POKEDEX, $Pokemon, 2, 1000, ';' );

            $Generation = Arrays::getIfSet ( $StaticData,  3, 1 );
            $Rarete     = Arrays::getIfSet ( $StaticData, 22, 'common' );
            $DropRate   = str_replace ( ',', '.', Arrays::getIfSet ( $StaticData, 19, 0 ) );
            
            TagedDBColl::execute ( "INSERT INTO " . self::TABLE_POKEMON . " (" . self::NOM . ", " . self::GENERATION . ", " . self::RARETE . ", " . self::DROP_RATE . ") VALUES ('" . $Pokemon . "', " . $Generation . ", '" . $Rarete . "', " . $DropRate . ");" );
        }
        elseif ( $DropRate == 0 )
        {
            $StaticData = Arrays::getCSVLine ( self::POKEDEX, $Pokemon, 2, 1000, ';' );
            
            $Generation = Arrays::getIfSet ( $StaticData,  3, 1 );
            $Rarete     = Arrays::getIfSet ( $StaticData, 22, 'common' );
            $DropRate   = str_replace ( ',', '.', Arrays::getIfSet ( $StaticData, 19, 0 ) );
            
            TagedDBColl::execute ( "UPDATE " . self::TABLE_POKEMON . " SET " . 
                self::GENERATION . " = " . $Generation . ", " .
                self::RARETE . " = '" . $Rarete . "'" . ", " .
                self::DROP_RATE . " = " . $DropRate . " " .
                "WHERE " . self::NOM . " = '" . $Pokemon . "';" );
        }
        
        $this->DropRate [$Pokemon] = $DropRate;
        
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function alignePokemon ( $Pokemon, $Ordre )
    {
        Log::fct_enter ( __METHOD__ );
        
        
        TagedDBColl::execute ( "INSERT INTO " . self::TABLE_ALIGNE . " (" . self::ID . ", " . self::NOM . ", " . self::ORDRE . ") VALUES (" . $this->IDEquipe . ", '" . $Pokemon . "', " . $Ordre . ");" );
        
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function getPokemonList ( $Length, &$DR )
    {
        Log::fct_enter ( __METHOD__ );
        $List = $this->Pokemons;
        $DR = 1;
//         Log::info ( __METHOD__ . ' ' . $Length );
        
        if ( $Length != 0 )
        {
            $List = array_slice ( $this->Pokemons, 0, $Length );
//             Log::info ( __METHOD__ . ' ' . print_r ( $List, TRUE ) );
        }
        
//         Log::info ( __METHOD__ . ' ' . print_r ( $this->DropRate, TRUE ) );
        
        foreach ( $List as $Pokemon )
        {
            $Rate = Arrays::getIfSet ( $this->DropRate, $Pokemon, 0 );
            $DR *= $Rate;
        }
        
//         Log::info ( __METHOD__ . ' ' . $DR );
        
        return implode ( ',', $List );
        Log::fct_exit ( __METHOD__ );
    }
    
    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        // Pour chaque Pokemon v�rifie s'il existe.
        // V�rifie s'il existe 1 team existant avec la m�me liste de Pokemon
        // Si pas de team, ajoute une entr�e Equipe
        // R�cup�re l'ID
        
        $Aligne = FALSE;
        
        $this->Size = count ( $this->Pokemons );
        
        foreach ( $this->Pokemons as $Pokemon )
        {
            $this->savePokemon ( $Pokemon );
        }
        
        $DRList  = 1;
        $DRList2 = 1;
        $DRList3 = 1;
        $DRList4 = 1;
        $DRList5 = 1;
        
        $ListPokemon  = $this->getPokemonList ( 0, $DRList  );
        $List2Pokemon = $this->getPokemonList ( 2, $DRList2 );
        $List3Pokemon = $this->getPokemonList ( 3, $DRList3 );
        $List4Pokemon = $this->getPokemonList ( 4, $DRList4 );
        $List5Pokemon = $this->getPokemonList ( 5, $DRList5 );
        
        // #1 v�rifie si une Equipe existe pour ce nom
        $Select = "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOMBRE . " = " . $this->Size . ' AND ' . self::LISTE . " = '" . $ListPokemon . "'";
        TagedDBColl::execute ( $Select );
        $Results = TagedDBColl::getResults ( );
        
        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entr�e Equipe
            $Aligne = TRUE;
            TagedDBColl::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOMBRE . ", " . self::LISTE . ", " . self::LISTE2 . ", " . self::LISTE3 . ", " . self::LISTE4 . ", " . self::LISTE5 . ", " . self::DROP_RATE . ", " . self::DROP_RATE2 . ", " . self::DROP_RATE3 . ", " . self::DROP_RATE4 . ", " . self::DROP_RATE5 . 
                ") VALUES (" . $this->Size . ", '" . $ListPokemon  . "', '" . $List2Pokemon  . "', '" . $List3Pokemon  . "', '" . $List4Pokemon  . "', '" . $List5Pokemon  . "', " . $DRList . ", " . $DRList2 . ", " . $DRList3 . ", " . $DRList4 . ", " . $DRList5 . ");" );

            TagedDBColl::execute ( $Select );
            $Results = TagedDBColl::getResults ( );
        }

        if ( ( NULL != $Results ) || ( count ( $Results ) > 0 ) )
        {
            $this->IDEquipe = Arrays::getIfSet ( $Results[0], self::ID, -1 );
            Log::debug ( json_encode ( $Results ) . ' ' . $this->IDEquipe );
            if ( $Aligne )
            {
                foreach ( $this->Pokemons as $Position => $Pokemon )
                {
                    $this->alignePokemon ( $Pokemon, $Position );
                }
            }
        }
        Log::fct_exit ( __METHOD__ );
    }
}

//Log::setDebug ( __FILE__ );
