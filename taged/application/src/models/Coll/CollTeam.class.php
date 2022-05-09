<?php

class CollTeam 
{
    const TABLE = 'equipe';
    const TABLE_ALIGNE = 'aligne';
    const TABLE_POKEMON = 'pokemon';
    
    const ID = 'id_equipe';
    const NOMBRE = 'nombre';
    const LISTE = 'liste';
    const ORDRE = 'ordre';
    const NOM = 'nom';
    
    /**
     * @var $Player is p1, p2, p3, or p4.
     */
    private $Player;

    /**
     * @var $Size is the number of PokÃ©mon your opponent starts with. In games without ReplayParser Preview, you don't know which PokÃ©mon your opponent has, but you at least know how many there are.
     */
    private $Size;
    
    /**
     * @var $Pokemons is the array of Pokemons, index by position
     */
    private $Pokemons;

    private $IDEquipe; // ID dans la table Equipe
    
    /**
     * TeamSize constructor.
     * @param int $Player The player position of the Team 
     * @param int $Size The Quantity of pokemons in the Team 
     */
    public function __construct ( $Player, $Size, $EquipeID = -1, $Pokemons = array () ) 
    {
        $this->Player = $Player;
        $this->Size = $Size;
        
        $this->IDEquipe = $EquipeID;
        $this->Pokemons = $Pokemons;
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
        $Result .= ')';
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
//         if ( ! in_array ( $Pokemon, $this->Pokemons ) )
//         {
            $this->Pokemons [ ] = $Pokemon;
//         }
    }

    protected function savePokemon ( $Pokemon )
    {
        Log::fct_enter ( __METHOD__ );
        
        TagedDB::execute ( "SELECT * FROM " . self::TABLE_POKEMON . " WHERE " . self::NOM . " = '" . $Pokemon ."'" );
        $Results = TagedDB::getResults ( );

        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entrée Utilisateur
            TagedDB::execute ( "INSERT INTO " . self::TABLE_POKEMON . " (" . self::NOM . ") VALUES ('" . $Pokemon . "');" );
        }
        
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function alignePokemon ( $Pokemon, $Ordre )
    {
        Log::fct_enter ( __METHOD__ );
        
        $this->savePokemon ( $Pokemon );
        
        TagedDB::execute ( "INSERT INTO " . self::TABLE_ALIGNE . " (" . self::ID . ", " . self::NOM . ", " . self::ORDRE . ") VALUES (" . $this->IDEquipe . ", '" . $Pokemon . "', " . $Ordre . ");" );
        
        Log::fct_exit ( __METHOD__ );
    }
    
    public function save ()
    {
        Log::fct_enter ( __METHOD__ );

        // Pour chaque Pokemon vérifie s'il existe.
        // Vérifie s'il existe 1 team existant avec la même liste de Pokemon
        // Si pas de team, ajoute une entrée Equipe
        // Récupère l'ID
        
        $Aligne = FALSE;
        
        $ListPokemon = implode ( ',', $this->Pokemons );
        
        // #1 vérifie si une Equipe existe pour ce nom
        $Select = "SELECT " . self::ID . " FROM " . self::TABLE . " WHERE " . self::NOMBRE . " = " . $this->Size . ' AND ' . self::LISTE . " = '" . $ListPokemon . "'";
        TagedDB::execute ( $Select );
        $Results = TagedDB::getResults ( );
        
        if ( ( NULL == $Results ) || ( count ( $Results ) == 0 ) )
        {
            // #2 Si non, ajoute entrée Equipe
            $Aligne = TRUE;
            TagedDB::execute ( "INSERT INTO " . self::TABLE . " (" . self::NOMBRE . ", " . self::LISTE . ") VALUES (" . $this->Size . ", '" . $ListPokemon  . "');" );

            TagedDB::execute ( $Select );
            $Results = TagedDB::getResults ( );
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
