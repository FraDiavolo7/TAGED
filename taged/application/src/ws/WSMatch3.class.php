<?php

/**
 * Service Web Match3
 * 
 * Gère toutes les données Match3 provenant du Match3.
 * @package TAGED\WebServices
 */
class WSMatch3 extends TagedWS
{
    /**
     * Constante représentant les données Match3.
     */
    const WS_M3_DATA = 'data';
    
    /**
     * Constante représentant le sélecteur Match3.
     */
    const WS_M3_SELECTOR = 'm3';
    
    /**
     * Constante représentant la valeur par défaut du sélecteur Match3.
     */
    const WS_M3_DEFAULT = 'nothing';
    
    /**
     * Liste des correspondances sélecteur Match3 - action.
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
     * Initialise un nouvel objet WSMatch3 en récupérant les données d'entrée passées en paramètre
     * ou en utilisant la superglobale $_REQUEST si aucun paramètre n'est fourni.
     * 
     * @param array|null $InputData Données d'entrée du service web Match3.
     */
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->InputData = ( NULL == $InputData ? $_REQUEST : $InputData );
	}

	/**
	 * Fonction principale du service web.
	 * 
	 * Exécute l'action associée au sélecteur Match3 (récupéré via la méthode getM3Selector) et retourne le résultat.
	 * 
	 * @return string Résultat de l'action du service web Match3.
	 */
	public function serve ()
	{
	    $Action = $this->getM3Selector ();
	    
	    return $this->$Action ();
	}
	
	/**
	 * Fonction de stockage des données.
	 * 
	 * Récupère les données du Match3, les traite et les sauvegarde dans la base de données en tant que partie de jeu.
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
	 * Action intermédiaire du Match3.
	 * 
	 * Appelle la méthode de stockage des données (store) et ne renvoie aucune valeur.
	 * 
	 * @return string Chaîne vide.
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
	 * Appelle la méthode de stockage des données (store) et ne renvoie aucune valeur.
	 * 
	 * @return string Chaîne vide.
	 */
	protected function gameOver ()
	{
	    Log::fct_enter ( __METHOD__ );
	    
	    $this->store ();
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	/**
	 * Action pour démarrer une nouvelle partie du Match3.
	 * 
	 * Récupère les données du Match3 concernant la nouvelle partie et ne renvoie aucune valeur.
	 * 
	 * @return string Chaîne vide.
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
	 * Obtient le sélecteur Match3.
	 * 
	 * Récupère le sélecteur Match3 des données d'entrée et retourne l'action associée dans la liste WS_M3_LIST.
	 * Si le sélecteur n'est pas trouvé dans la liste, retourne l'action par défaut WS_M3_DEFAULT.
	 * 
	 * @return string Action associée au sélecteur Match3.
	 */
	protected function getM3Selector ()
	{
	    $Selector = Arrays::getIfSet ( $this->InputData, self::WS_M3_SELECTOR, self::WS_M3_DEFAULT );
	    
	    $Action = Arrays::getIfSet ( self::WS_M3_LIST, $Selector, self::WS_M3_DEFAULT );
	    
	    return $Action;
	}
	
	/**
	 * Données d'entrée du service web Match3.
	 * @var array
	 */
	protected $InputData;
} // WSMatch3

