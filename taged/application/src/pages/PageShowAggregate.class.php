<?php

/**
 * Cette classe représente une page pour afficher une agrégation.
 *
 * @package TAGED\Pages
 */
class PageShowAggregate extends TagedPage
{
    /**
     * Constante pour définir la clé permettant de récupérer la valeur d'agrégation sélectionnée à partir des données d'entrée de l'utilisateur.
     *
     * @var string
     */
    const SHOW_AGGREGATE = 'change_aggregate';
    
    /**
     * Constante pour définir la clé permettant de récupérer le mot de passe saisi par l'utilisateur.
     *
     * @var string
     */
    const SHOW_PASSWORD = 'change_password';
    
    /**
     * Constante pour définir la clé pour soumettre les données du formulaire.
     *
     * @var string
     */
    const SHOW_SUBMIT = 'change_submit';
    
    /**
     * Initialise l'objet PageShowAggregate.
     *
     * @param mixed $InputData Les données d'entrée pour initialiser la page. Si null, les données $_REQUEST seront utilisées.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Afficher Aggregation';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		$this->Result = NULL;
		
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère la soumission du formulaire et traite les données d'entrée.
	 *
	 * @param mixed $Data Les données d'entrée à traiter.
	 */
	protected function handle ( $Data )
	{
	    $Submit = Form::getData ( self::SHOW_SUBMIT, '', $Data );
        $this->Aggregate = Form::getData ( self::SHOW_AGGREGATE, '', $Data );
        $AggregateFile = NULL;
        
        if ( '' != $this->Aggregate )
        {
            $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini' );
        }
	    if ( $Submit != '' )
	    {
	        $Password = Form::getData ( self::SHOW_PASSWORD, '', $Data );
	        
	        if ( $Password == $this->Password )
	        {
	            $this->Result = $this->AggregateObj->getAggregateFile ( FALSE );
	        }
	    }
	    
	    $this->show ();
	}
	
	/**
	 * Affiche le contenu de la page.
	 */
	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::SHOW_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::SHOW_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Submit = HTML::div ( HTML::submit( self::SHOW_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    
	    $Result = '';
	    
	    if ( NULL != $this->Result )
	    {
	        $Result = HTML::div ( $this->Result->show (), array ( 'class' => 'coll_data' ) );
	    }

        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= $Submit;
            $Content .= $Result;
        }

	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}

	/**
	 * Le résultat de l'opération d'agrégation.
	 *
	 * @var mixed
	 */
	protected $Result;
	
	/**
	 * Le mot de passe pour l'authentification afin de voir l'agrégation.
	 *
	 * @var string
	 */
	protected $Password;
	
	/**
	 * La valeur d'agrégation sélectionnée par l'utilisateur.
	 *
	 * @var string
	 */
	protected $Aggregate;
	
	/**
	 * L'objet Analysis associé à l'agrégation sélectionnée.
	 *
	 * @var Analysis|null
	 */
	protected $AggregateObj;
	
	/**
	 * L'objet représentant la liste des agrégations disponibles.
	 *
	 * @var AggregateList
	 */
	protected $AggregateListObj;
	
	/**
	 * Le tableau associatif des agrégations disponibles avec leurs identifiants.
	 *
	 * @var array
	 */
	protected $AggregateList;
} // PageShowAggregate
