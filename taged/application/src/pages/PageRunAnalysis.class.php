<?php

/**
 * Page de base pour le lancement d'analyse dans TAGED.
 *
 * @package TAGED\Pages
 */
class PageRunAnalysis extends TagedPage
{
    /**
     * Clé pour l'agrégation sélectionnée.
     * @var string
     */
    const RAN_AGGREGATE = 'ran_aggregate';
    
    /**
     * Clé pour le mot de passe saisi.
     * @var string
     */
    const RAN_PASSWORD = 'ran_password';
    
    /**
     * Clé pour l'algorithme sélectionné.
     * @var string
     */
    const RAN_ALGO = 'ran_algo';
    
    /**
     * Clé pour le bouton de soumission.
     * @var string
     */
    const RAN_SUBMIT = 'ran_submit';
    
    /**
     * Clé pour afficher le test.
     * @var string
     */
    const SHOW_TEST = 'ran_test';
    
    /**
     * Clé pour la valeur minimale.
     * @var string
     */
    const RAN_MIN = 'ran_min';
    
    /**
     * Clé pour la valeur maximale.
     * @var string
     */
    const RAN_MAX = 'ran_max';
    
    /**
     * Clé pour afficher l'entrée.
     * @var string
     */
    const SHOW_INPUT = 'ran_input';
    
    /**
     * Clé pour afficher l'espace multidimensionnel.
     * @var string
     */
    const SHOW_ESPACE = 'ran_espace';
    
    /**
     * Clé pour afficher les accords.
     * @var string
     */
    const SHOW_ACCORDS = 'ran_accords';
    
    /**
     * Clé pour afficher le DataCube.
     * @var string
     */
    const SHOW_DATACUBE = 'ran_skycube';
    
    /**
     * Clé pour afficher le SkyCube.
     * @var string
     */
    const SHOW_SKYCUBE = 'ran_sc_red';
    
    /**
     * Clé pour afficher la relation fusionnée.
     * @var string
     */
    const SHOW_FUSION = 'ran_fusion';
    
    /**
     * Clé pour afficher la relation fusionnée abrégée.
     * @var string
     */
    const SHOW_FUS_ABREGE = 'ran_fus_abr';
    
    /**
     * Clé pour afficher le classement des Skyline (CoSky).
     * @var string
     */
    const SHOW_COSKY = 'ran_cosky';
    
    /**
     * Clé pour afficher le résultat Taged.
     * @var string
     */
    const SHOW_TAGED_CUBE = 'ran_sc_tag';
    
    /**
     * Constructeur de la classe PageRunAnalysis.
     * @param mixed $InputData Données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Lancer Analyse';
		$this->Result = '';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		
		$this->AlgoList = Algo::generateList ();
        $this->Algo = '';
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère les données d'entrée et le lancement de l'analyse.
	 * @param array $Data Données d'entrée.
	 */
	protected function handle ( $Data )
	{
    	$Submit = Form::getData ( self::RAN_SUBMIT, '', $Data );
	    
    	$this->Aggregate = Form::getData ( self::RAN_AGGREGATE, '', $Data );
    	$this->Algo = Form::getData ( self::RAN_ALGO, '', $Data );
    	$this->ShowInput         = Form::getData ( self::SHOW_INPUT,      FALSE, $Data );
    	$this->ShowEspace        = Form::getData ( self::SHOW_ESPACE,     FALSE, $Data );
    	$this->ShowAccords       = Form::getData ( self::SHOW_ACCORDS,    FALSE, $Data );
    	$this->ShowDataCube      = Form::getData ( self::SHOW_DATACUBE,   FALSE, $Data );
    	$this->ShowSkyCube       = Form::getData ( self::SHOW_SKYCUBE,    FALSE, $Data );
    	$this->ShowFusion        = Form::getData ( self::SHOW_FUSION,     FALSE, $Data );
    	$this->ShowFusionAbregee = Form::getData ( self::SHOW_FUS_ABREGE, FALSE, $Data );
    	$this->ShowCoSky         = Form::getData ( self::SHOW_COSKY,      FALSE, $Data );
    	$this->ShowTagedCube     = Form::getData ( self::SHOW_TAGED_CUBE, FALSE, $Data );
    	$this->Test              = Form::getData ( self::SHOW_TEST,       FALSE, $Data );
    	$this->Min               = Form::getData ( self::RAN_MIN,      array (), $Data );
    	$this->Max               = Form::getData ( self::RAN_MAX,      array (), $Data );
    	$AggregateFile = NULL;
    	
    	if ( '' != $this->Aggregate )
    	{
    	    $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini', $this->Test );
    	    $this->AggregateObj->prepare ( );
    	}
    	
	    if ( $Submit != '' )
	    {
	        $Password = Form::getData ( self::RAN_PASSWORD, '', $Data );
	        
//            if ( $Password == $this->Password ) 
            if ( TRUE )
            {
	            $this->AggregateObj->setAlgorithm ( $this->Algo );
	            if ( ! $this->Test )
	            {
	                $this->AggregateObj->setMin ( $this->Min );
	                $this->AggregateObj->setMax ( $this->Max );
	            }
	            $this->AggregateObj->compute ();
	            
	            if ( $this->ShowCoSky ) 
	            {
	                $this->CoSky1 = new CoSky ( $this->AggregateObj->getSkyCube ()->getSkyCube1 () );
	                $this->CoSky2 = new CoSky ( $this->AggregateObj->getSkyCube ()->getSkyCube2 () );
	                
	                $this->CoSky1->run ();
	                $this->CoSky2->run ();
	            }
	            
                $this->Result = $this->AggregateObj->getResult ();
	        }
	    }
	    
	    $this->show ();
	}
	
	/**
	 * Affiche le contenu de la page.
	 */
	protected function show ( )
	{
	    $Password  = HTML::div ( HTML::inputPassword ( self::RAN_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::RAN_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Algo      = HTML::div ( HTML::select ( self::RAN_ALGO, $this->AlgoList, $this->Algo ), array ( 'class' => 'algo' ) );
	    $Submit    = HTML::div ( HTML::submit ( self::RAN_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    $CheckBoxes  = '';
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_TEST, 1, 'Test', $this->Test ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_INPUT, 1, 'Entr&eacute;e', $this->ShowInput ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ESPACE, 1, 'Espace Multidimensionnel', $this->ShowEspace ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ACCORDS, 1, 'Classes d&apos;accords', $this->ShowAccords ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_DATACUBE, 1, 'DataCube', $this->ShowDataCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_SKYCUBE, 1, 'SkyCube', $this->ShowSkyCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_FUSION, 1, 'Relation Fusionn&eacute;e', $this->ShowFusion ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_FUS_ABREGE, 1, 'Relation Fusionn&eacute;e abr&eacute;g&eacute;e', $this->ShowFusionAbregee ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_COSKY, 1, 'Classement des Skyline (CoSky)', $this->ShowCoSky ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_TAGED_CUBE, 1, 'R&eacute;sultat Taged', $this->ShowTagedCube ), array ( 'class' => 'checkbox' ) );
	    
        $Content = $Aggregate;
        $SkyCube = NULL;

        if ( '' != $this->Aggregate )
        {
            $SkyCube = $this->AggregateObj->getSkyCube ();
            
            $ColIDs = $SkyCube->getDenumberedColIDs ();
            $MinMaxHeader = HTML::th ( '' );
            $Min = HTML::th ( 'Min' );
            $Max = HTML::th ( 'Max' );
            
            foreach ( $ColIDs as $Full => $Coded )
            {
                $MinMaxHeader .= HTML::th ( $Full );
                $Min .= HTML::td ( HTML::inputText ( self::RAN_MIN . "[$Coded]", $this->Min [$Coded] ?? 0 ) );
                $Max .= HTML::td ( HTML::inputText ( self::RAN_MAX . "[$Coded]", $this->Max [$Coded] ?? 0 ) );
            }
            
            $MinMax = HTML::div ( HTML::table (
                HTML::tr ( $MinMaxHeader ) . 
                HTML::tr ( $Min ) .
                HTML::tr ( $Max )
                ) );
            
            $Content .= $Password;
            $Content .= HTML::div ( $CheckBoxes, array ( 'class' => 'checkboxes' ) );
            $Content .= $Algo;
            $Content .= $Submit;
            $Content .= $MinMax;
        }
         
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	    
	    if ( '' != $this->Result )
	    {
    	    $Result = HTML::div ( $this->Result, array ( 'class' => 'result_msg' ) );
    	    
    	    if ( NULL != $SkyCube )
    	    {
    	        if ( $this->ShowInput     )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Entr&eacute;e', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlInputData             ( $SkyCube ), array ( 'class' => 'result result_input' ) );
    	        }
    	        
    	        if ( $this->ShowEspace    )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Espace multidimensionel', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlMultidimensionalSpace ( $SkyCube ), array ( 'class' => 'result result_espace' ) );
    	        }
    	        
    	        if ( $this->ShowDataCube  )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'DataCube', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_DATA_RAW ), array ( 'class' => 'result result_datacube' ) );
    	            $Result .= HTML::div ( HTML::title ( 'DataCube 1', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube1 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_DATA_RAW | SKDisplay::SHOW_REMOVED ), array ( 'class' => 'result result_datacube' ) );
    	            $Result .= HTML::div ( HTML::title ( 'DataCube 2', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube2 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_DATA_RAW | SKDisplay::SHOW_REMOVED ), array ( 'class' => 'result result_datacube' ) );
    	        }
    	        
    	        if ( $this->ShowSkyCube   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Trellis du SkyCube 1', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube1 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_DATA_FILTERED | SKDisplay::SHOW_REMOVED /* | SKDisplay::SHOW_EQUIV_CLASS */ ), array ( 'class' => 'result result_skycube' ) );
    	            $Result .= HTML::div ( HTML::title ( 'Trellis du SkyCube 2', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube2 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_DATA_FILTERED | SKDisplay::SHOW_REMOVED /* | SKDisplay::SHOW_EQUIV_CLASS */ ), array ( 'class' => 'result result_skycube' ) );
    	        }
    	        
    	        if ( $this->ShowAccords   ) 
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Concepts Acccords SkyCube 1', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube1 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS ), array ( 'class' => 'result result_accords' ) );
    	            $Result .= HTML::div ( HTML::title ( 'Concepts Acccords SkyCube 2', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube2 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS ), array ( 'class' => 'result result_accords' ) );
    	            $Result .= HTML::div ( HTML::title ( 'Concepts Skylines SkyCube 1', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube1 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS_FILTERED ), array ( 'class' => 'result result_accords' ) );
    	            $Result .= HTML::div ( HTML::title ( 'Concepts Skylines SkyCube 2', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube->getSkyCube2 (), SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS_FILTERED ), array ( 'class' => 'result result_accords' ) );
    	        }
    	        
    	        if ( $this->ShowAccords   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Concepts Acccords', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS ), array ( 'class' => 'result result_accords' ) );
    	        }
    	        
    	        if ( $this->ShowSkyCube   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'SkyCube', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_DATA_FILTERED ), array ( 'class' => 'result result_skycube' ) );
    	        }
    	        
    	        if ( $this->ShowFusion   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Relation Fusionn&eacute;e', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeFusion ( $SkyCube ), array ( 'class' => 'result result_skycube' ) );
    	        }
    	        
    	        if ( $this->ShowFusionAbregee   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Relation Fusionn&eacute;e Abr&eacute;g&eacute;e', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeFusion ( $SkyCube, SKDisplay::SHOW_FILTERED ), array ( 'class' => 'result result_skycube' ) );
    	        }

    	        if ( $this->ShowCoSky )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Classement des Skylines 1 (CoSky)', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlCoSky ( $this->CoSky1 ), array ( 'class' => 'result result_cosky' ) );
    	            $Result .= HTML::div ( HTML::title ( 'Classement des Skylines 2 (CoSky)', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlCoSky ( $this->CoSky2 ), array ( 'class' => 'result result_cosky' ) );
    	        }
    	        
    	        if ( $this->ShowTagedCube )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Emergence', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlEmergence ( $SkyCube ), array ( 'class' => 'result result_emergence' ) );
    	        }
    	    }
    	    $this->add ( HTML::div ( $Result, array ( 'class' => 'results' ) ) );
	    }
	    
	}
	
	/**
	 * Clé pour récupérer le mot de passe entré par l'utilisateur.
	 *
	 * @var string
	 */
	protected $Password;
	
	/**
	 * Clé pour récupérer l'agrégat sélectionné par l'utilisateur.
	 *
	 * @var string
	 */
	protected $Aggregate;
	
	/**
	 * Objet Analysis associé à l'agrégat sélectionné.
	 *
	 * @var Analysis|null
	 */
	protected $AggregateObj;
	
	/**
	 * Liste des agrégats disponibles.
	 *
	 * @var AggregateList
	 */
	protected $AggregateListObj;
	
	/**
	 * Tableau associatif des agrégats disponibles avec leurs identifiants.
	 *
	 * @var array
	 */
	protected $AggregateList;
	
	/**
	 * Indicateur pour activer ou désactiver la section de test dans les résultats.
	 *
	 * @var bool
	 */
	protected $Test;
	
	/**
	 * Tableau associatif des valeurs minimales pour les colonnes de l'agrégat.
	 *
	 * @var array
	 */
	protected $Min;
	
	/**
	 * Tableau associatif des valeurs maximales pour les colonnes de l'agrégat.
	 *
	 * @var array
	 */
	protected $Max;
	
	/**
	 * Objet CoSky pour le classement des Skylines du SkyCube 1.
	 *
	 * @var CoSky|null
	 */
	protected $CoSky1;
	
	/**
	 * Objet CoSky pour le classement des Skylines du SkyCube 2.
	 *
	 * @var CoSky|null
	 */
	protected $CoSky2;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Entrée" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowInput;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Espace Multidimensionnel" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowEspace;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Classes d'accords" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowAccords;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "DataCube" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowDataCube;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "SkyCube" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowSkyCube;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Relation Fusionnée" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowFusion;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Relation Fusionnée abrégée" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowFusionAbregee;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Classement des Skyline (CoSky)" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowCoSky;
	
	/**
	 * Indicateur pour activer ou désactiver l'affichage de la section "Résultat Taged" dans les résultats.
	 *
	 * @var bool
	 */
	protected $ShowTagedCube;
	
} // PageRunAnalysis
