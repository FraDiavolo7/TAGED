<?php

/**
 * Classe représentant une page permettant de créer une agrégation de données.
 *
 * @package TAGED\Pages
 */
class PageNewAggregate extends TagedPage
{
    /**
     * Clé pour récupérer le nom de la base de données de jeu à utiliser.
     * @var string
     */
    const NAG_DB = 'nag_game';
    
    /**
     * Clé pour récupérer le nom de l'agrégation à créer.
     * @var string
     */
    const NAG_AGGREGATE = 'nag_aggregate';
    
    /**
     * Clé pour récupérer le nom de la table en base de données à utiliser.
     * @var string
     */
    const NAG_TABLE = 'nag_table';
    
    /**
     * Clé pour récupérer les attributs de relation à associer à l'agrégation.
     * @var string
     */
    const NAG_REL_COLS = 'nag_relation_cols';
    
    /**
     * Clé pour récupérer les attributs de mesure à associer à l'agrégation.
     * @var string
     */
    const NAG_MES_COLS = 'nag_measure_cols';
    
    /**
     * Clé pour récupérer le mot de passe nécessaire pour créer l'agrégation.
     * @var string
     */
    const NAG_PASSWORD = 'nag_password';
    
    /**
     * Clé pour récupérer le nom de l'agrégation à créer lors de la soumission du formulaire.
     * @var string
     */
    const NAG_SUBMIT = 'nag_submit';
    
    /**
     * Constructeur de la classe PageNewAggregate.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Créer Aggregation';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		
		$Games = APP_NAMES;
		$this->Games = array ();
		
		foreach ( $Games as $Game )
		{
		    $this->Games [$Game] = Strings::convertCase ( $Game, Strings::CASE_NATURAL, Strings::CASE_CAMEL_LOWER );
		}
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère les données entrées dans le formulaire pour créer une agrégation.
	 *
	 * @param mixed $Data Les données du formulaire.
	 */
	protected function handle ( $Data )
	{
// 	    $this->add ( HTML::div ( print_r ( $Data, true ) ) );
	    
	    $Submit = Form::getData ( self::NAG_SUBMIT, '', $Data );
        
	    if ( $Submit != '' )
	    {
	        $this->Aggregate = Form::getData ( self::NAG_AGGREGATE, '', $Data );
	        $Password        = Form::getData ( self::NAG_PASSWORD, '', $Data );
	        
	        if ( ( $Password == $this->Password ) && ( '' != $this->Aggregate ) ) 
	        {
	            $Games        = Form::getData ( self::NAG_DB,       NULL, $Data );
	            if ( NULL != $Games )
	            {
	                $Table        = Form::getData ( self::NAG_TABLE,    NULL, $Data );
	                $RelationCols = Form::getData ( self::NAG_REL_COLS, '',   $Data );
	                $MeasureCols  = Form::getData ( self::NAG_MES_COLS, '',   $Data );
	                $File = NULL;
	                
	                if ( NULL == $Table )
	                {
	                    $File = $this->Aggregate;
	                }
	                Analysis::create ( $this->Aggregate, $Games, $RelationCols, $MeasureCols, $File, $Table );
	                if ( $Table != NULL )
	                {
	                    $this->add ( HTML::redirect ( Menu::buildGenLink ( 'GestAggregate' ) ) );
	                }
	                else
	                {
	                    $this->add ( HTML::redirect ( Menu::buildGenLink ( 'ChangeAggregate', array ( PageChangeAggregate::CHANGE_AGGREGATE => $this->Aggregate ) ) ) );
	                }
	            }
	            else
	            {
	                $this->add ( HTML::div ( "Jeu non fourni", array ( 'class' => 'error' ) ) );
	            }
	        }
	        else
	        {
	            $this->add ( HTML::div ( "Same player shoot again", array ( 'class' => 'error' ) ) );
	        }
	    }
	    else 
	    {
	        $this->show ();
	    }
	}
	
	/**
	 * Affiche le formulaire pour créer une agrégation de données.
	 */
	protected function show ( )
	{
        $Content = '';
        
	    $Games     = HTML::div ( HTML::select        ( self::NAG_DB, $this->Games, $this->Aggregate, ), array ( 'class' => 'games' ) );
	    $Password  = HTML::div ( HTML::inputPassword ( self::NAG_PASSWORD,  ''                ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::inputText     ( self::NAG_AGGREGATE, '', 'Nom'         ), array ( 'class' => 'aggregate' ) );
	    $Table     = HTML::div ( HTML::inputText     ( self::NAG_TABLE,     '', 'Table en BD' ) . ' Si vide, vous pourrez entrer une requête personnalisée', array ( 'class' => 'aggregate' ) );
	    $RelCols   = HTML::div ( HTML::inputText     ( self::NAG_REL_COLS,  '', 'Attributs de la Relation' ), array ( 'class' => 'aggregate' ) );
	    $MesCols   = HTML::div ( HTML::inputText     ( self::NAG_MES_COLS,  '', 'Attributs de Mesure' ), array ( 'class' => 'aggregate' ) );
	    $Submit    = HTML::div ( HTML::submit        ( self::NAG_SUBMIT,    "Créer"           ), array ( 'class' => 'submit' ) );

	    $Content .= $Games;
	    $Content .= $Aggregate;
        $Content .= $Password;
        $Content .= $Table;
        $Content .= $RelCols;
        $Content .= $MesCols;
        $Content .= $Submit;

	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}
	
	/**
	 * Liste des jeux disponibles pour créer une agrégation.
	 *
	 * @var array
	 */
	protected $Games;
	
	/**
	 * Le mot de passe requis pour créer une agrégation.
	 *
	 * @var string
	 */
	protected $Password;
	
	/**
     * Nom de l'agrégation en cours de création.
	 *
	 * @var string
	 */
	protected $Aggregate;
} // PageNewAggregate
