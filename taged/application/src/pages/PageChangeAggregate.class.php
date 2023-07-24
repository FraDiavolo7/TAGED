<?php

/**
 * Classe représentant la page de modification des agrégations.
 *
 * @package TAGED\Pages
 */
class PageChangeAggregate extends TagedPage
{
    /**
     * Constante représentant l'action "Modifier Aggregation".
     */
    const CHANGE_AGGREGATE = 'change_aggregate';
    
    /**
     * Constante représentant l'action "Modifier Mot de passe".
     */
    const CHANGE_PASSWORD = 'change_password';
    
    /**
     * Constante représentant l'action "Modifier la Requête".
     */
    const CHANGE_REQUEST = 'change_request';
    
    /**
     * Constante représentant l'action "Modifier les Colonnes de Relation".
     */
    const CHANGE_REL_COLS  = 'change_relation_cols';
    
    /**
     * Constante représentant l'action "Modifier les Colonnes de Mesure".
     */
    const CHANGE_MES_COLS  = 'change_measure_cols';
    
    /**
     * Constante représentant l'action "Envoyer les Modifications".
     */
    const CHANGE_SUBMIT = 'change_submit';
    
    /**
     * Constructeur de la classe PageChangeAggregate.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Modifier Aggregation';
		$this->Request = '';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère les actions de la page.
	 *
	 * @param array $Data Les données de la requête.
	 */
	protected function handle ( $Data )
	{
// 	    $this->add ( HTML::div ( print_r ( $Data, true ) ) );
	    
	    $Submit = Form::getData ( self::CHANGE_SUBMIT, '', $Data );
        $this->Aggregate = Form::getData ( self::CHANGE_AGGREGATE, '', $Data );
        $AggregateFile = NULL;
        
        if ( '' != $this->Aggregate )
        {
            $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini' );
            $AggregateFile = $this->AggregateObj->getAggregateFile ( FALSE );
            $this->Request = $AggregateFile->getRequest ();
        }
	    if ( $Submit != '' )
	    {
	        $this->Request = Form::getData ( self::CHANGE_REQUEST, '', $Data );
	        $RelCols = Form::getData ( self::CHANGE_REL_COLS, '', $Data );
	        $MesCols = Form::getData ( self::CHANGE_MES_COLS, '', $Data );
	        $Password = Form::getData ( self::CHANGE_PASSWORD, '', $Data );
	        
	        if ( ( $Password == $this->Password ) && ( NULL != $AggregateFile ) ) 
	        {
	            $this->AggregateObj->setRelationCols ( $RelCols );
	            $this->AggregateObj->setMeasureCols ( $MesCols );
	            $this->AggregateObj->write ( );
	            $AggregateFile->setRequest ( $this->Request );
	            echo "Updating Request for " . $this->Aggregate;
	        }
	    }
	    
	    $this->show ();
	}
	
	/**
	 * Affiche le contenu de la page.
	 */
	protected function show ( )
	{
	    if ( '' == $this->Request ) $this->Request = ' ' ;
	    $Password = HTML::div ( HTML::inputPassword ( self::CHANGE_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::CHANGE_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    
	    $Request = '';
	    $RelCols = '';
	    $MesCols = '';
	    
	    if ( NULL != $this->AggregateObj )
	    {
	        $RelCols = HTML::div ( HTML::inputText ( self::CHANGE_REL_COLS, $this->AggregateObj->getRelationCols (), 'Attributs de la Relation' ), array ( 'class' => 'aggregate' ) );
	        $MesCols = HTML::div ( HTML::inputText ( self::CHANGE_MES_COLS, $this->AggregateObj->getMeasureCols (), 'Attributs de Mesure' ), array ( 'class' => 'aggregate' ) );
	        $Request = HTML::div ( HTML::textarea  ( self::CHANGE_REQUEST,  $this->Request, 35, 160 ), array ( 'class' => 'request' ) );
	    }
	        
	    $Submit = HTML::div ( HTML::submit( self::CHANGE_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );

        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= $RelCols;
            $Content .= $MesCols;
            $Content .= $Request;
            $Content .= $Submit;
        }

	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}

	/**
	 * La requête.
	 *
	 * @var string $Request La requête à modifier.
	 */
	protected $Request;
	
	/**
	 * Le mot de passe.
	 *
	 * @var string $Password Le mot de passe de vérification.
	 */
	protected $Password;
	
	/**
	 * Le nom de l'agrégation sélectionnée.
	 *
	 * @var string $Aggregate Le nom de l'agrégation sélectionnée.
	 */
	protected $Aggregate;
	
	/**
	 * L'objet de l'agrégation sélectionnée.
	 *
	 * @var Analysis|null $AggregateObj L'objet de l'agrégation sélectionnée.
	 */
	protected $AggregateObj;
	
	/**
	 * L'objet de la liste des agrégations.
	 *
	 * @var AggregateList $AggregateListObj L'objet de la liste des agrégations.
	 */
	protected $AggregateListObj;
	
	/**
	 * La liste des agrégations.
	 *
	 * @var array $AggregateList La liste des agrégations.
	 */
	protected $AggregateList;
} // PageChangeAggregate
