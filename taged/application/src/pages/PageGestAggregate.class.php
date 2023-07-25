<?php

/**
 * Classe représentant la page de gestion des agrégations.
 *
 * @package TAGED\Pages
 */
class PageGestAggregate extends TagedPage
{
    /**
     * Constante représentant le nom du champ d'agrégation.
     */
    const GAG_AGGREGATE = 'gag_aggregate';
    
    /**
     * Constante représentant le nom du champ de mot de passe.
     */
    const GAG_PASSWORD = 'gag_password';
    
    /**
     * Constante représentant le nom du champ de soumission du formulaire.
     */
    const GAG_SUBMIT = 'gag_submit';
    
    /**
     * Constructeur de la classe PageGestAggregate.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Gestion Aggregations';
		$this->Password = 'EmrakulUnMouton';
		
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère le traitement des données d'entrée.
	 *
	 * @param mixed $Data Les données d'entrée pour la page.
	 */
	protected function handle ( $Data )
	{
	    $Submit = Form::getData ( self::GAG_SUBMIT, '', $Data );

	    if ( $Submit != '' )
	    {
	        $this->Aggregate = Form::getData ( self::GAG_AGGREGATE, '', $Data );
	        $Password        = Form::getData ( self::GAG_PASSWORD,  '', $Data );
	        
            if ( ( $Password == $this->Password ) && ( NULL != $Aggregate ) ) 
	        {
	            Analysis::delete ( $this->Aggregate );
	        }
	    }
	    
	    $this->show ();
	}
	
	/**
	 * Affiche le contenu de la page.
	 */
	protected function show ( )
	{
	    $AggregateListObj = new AggregateList ();
	    $AggregateList = $AggregateListObj->getList ();
	    
        $TableContent = HTML::tr ( 
            HTML::th ( 'Aggregat', array ( 'class' => 'agg_label' ) ) .
            HTML::th ( 'Actions', array ( 'class' => 'agg_actions' ) )
            );

        foreach ( $AggregateList as $Name => $Label )
        {
            $TableContent .= HTML::tr (
                HTML::td ( $Label, array ( 'class' => 'agg_label' ) ) .
                HTML::td ( 
                    HTML::link ( Menu::buildGenLink ( 'RunAnalysis',     array ( PageRunAnalysis::RAN_AGGREGATE        => $Name ) ), "Analyser"  ) .
                    HTML::link ( Menu::buildGenLink ( 'ShowAggregate',   array ( PageShowAggregate::SHOW_AGGREGATE     => $Name ) ), "Afficher"  ) .
                    HTML::link ( Menu::buildGenLink ( 'ChangeAggregate', array ( PageChangeAggregate::CHANGE_AGGREGATE => $Name ) ), "Modifier"  ) .
                    HTML::link ( Menu::buildGenLink ( 'GestAggregate',   array ( self::GAG_AGGREGATE                   => $Name ) ), "Supprimer" ), 
                    array ( 'class' => 'agg_actions' )
                    )
                );
        }
        
        $this->add ( HTML::link ( Menu::buildGenLink ( 'NewAggregate' ), "Ajouter" ) );
        $this->add ( HTML::div ( HTML::table ( $TableContent ) , array ( 'class' => 'taged_gest_aggregates' ) ) ); 
	}

	/**
	 * Le mot de passe requis pour supprimer une agrégation.
	 *
	 * @var string
	 */
	protected $Password;
} // PageGestAggregate
