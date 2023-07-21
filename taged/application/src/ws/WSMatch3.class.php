<?php

/**
 * Service Web Match3
 * 
 * G�re toutes les donn�es Match3 provenant du Match3.
 * @package TAGED\WebServices
 */
class WSMatch3 extends TagedWS
{
    /**
     * Constante repr�sentant les donn�es Match3.
     */
    const WS_M3_DATA = 'data';
    
    /**
     * Constante repr�sentant le s�lecteur Match3.
     */
    const WS_M3_SELECTOR = 'm3';
    
    /**
     * Constante repr�sentant la valeur par d�faut du s�lecteur Match3.
     */
    const WS_M3_DEFAULT = 'nothing';
    
    /**
     * Liste des correspondances s�lecteur Match3 - action.
     * @var array
     */
    const WS_M3_LIST = array (
        'fin' => 'gameOver',
        'step' => 'intermediate',
        'new' => 'newGame'
    );
    
    /**
     * Constructeur de la classe WSMatch3.
     * 
     * Initialise un nouvel objet WSMatch3 en r�cup�rant les donn�es d'entr�e pass�es en param�tre
     * ou en utilisant la superglobale $_REQUEST si aucun param�tre n'est fourni.
     * 
     * @param array|null $InputData Donn�es d'entr�e du service web Match3.
     */
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->InputData = ( NULL == $InputData ? $_REQUEST : $InputData );
	}

	/**
	 * Fonction principale du service web.
	 * 
	 * Ex�cute l'action associ�e au s�lecteur Match3 (r�cup�r� via la m�thode getM3Selector) et retourne le r�sultat.
	 * 
	 * @return string R�sultat de l'action du service web Match3.
	 */
	public function serve ()
	{
	    $Action = $this->getM3Selector ();
	    
	    return $this->$Action ();
	}
	
	/**
	 * Fonction de stockage des donn�es.
	 * 
	 * R�cup�re les donn�es du Match3, les traite et les sauvegarde dans la base de donn�es en tant que partie de jeu.
	 */
	protected function store ()
	{
	    Log::fct_enter ( __METHOD__ );
	    $RawData = Arrays::getIfSet ( $this->InputData, self::WS_M3_DATA, '{}' );
	    $Data = json_decode ( $RawData, true );
	    
// 	    Log::debug ( __METHOD__ . ' ' . print_r ( $Data, TRUE ) );
	    
	    $Game = M3Game::build ( $Data );
	    
// 	    Log::debug ( __METHOD__ . ' ' . $Game );
	    
	    if ( NULL != $Game ) $Game->save ();
	    Log::fct_exit ( __METHOD__ );
	}

	/**
	 * Action interm�diaire du Match3.
	 * 
	 * Appelle la m�thode de stockage des donn�es (store) et ne renvoie aucune valeur.
	 * 
	 * @return string Cha�ne vide.
	 */
	protected function intermediate ()
	{
	    Log::fct_enter ( __METHOD__ );
	    
	    $this->store ();
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	/**
	 * Action de fin de partie du Match3.
	 * 
	 * Appelle la m�thode de stockage des donn�es (store) et ne renvoie aucune valeur.
	 * 
	 * @return string Cha�ne vide.
	 */
	protected function gameOver ()
	{
	    Log::fct_enter ( __METHOD__ );
	    
	    $this->store ();
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	/**
	 * Action pour d�marrer une nouvelle partie du Match3.
	 * 
	 * R�cup�re les donn�es du Match3 concernant la nouvelle partie et ne renvoie aucune valeur.
	 * 
	 * @return string Cha�ne vide.
	 */
	protected function newGame ()
	{
	    Log::fct_enter ( __METHOD__ );

// 	    Log::debug ( __METHOD__ . ' ' . self::WS_M3_DATA . ' ' . print_r ( $this->InputData, TRUE ) );
	    
	    $RawData = Arrays::getIfSet ( $this->InputData, self::WS_M3_DATA, '{}' );
	    $Data = json_decode ( $RawData, true );
	    
// 	    Log::debug ( __METHOD__ . ' ' . print_r ( $Data, TRUE ) );
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	/**
	 * Obtient le s�lecteur Match3.
	 * 
	 * R�cup�re le s�lecteur Match3 des donn�es d'entr�e et retourne l'action associ�e dans la liste WS_M3_LIST.
	 * Si le s�lecteur n'est pas trouv� dans la liste, retourne l'action par d�faut WS_M3_DEFAULT.
	 * 
	 * @return string Action associ�e au s�lecteur Match3.
	 */
	protected function getM3Selector ()
	{
	    $Selector = Arrays::getIfSet ( $this->InputData, self::WS_M3_SELECTOR, self::WS_M3_DEFAULT );
	    
	    $Action = Arrays::getIfSet ( self::WS_M3_LIST, $Selector, self::WS_M3_DEFAULT );
	    
	    return $Action;
	}
	
	/**
	 * Donn�es d'entr�e du service web Match3.
	 * @var array
	 */
	protected $InputData;
} // WSMatch3

