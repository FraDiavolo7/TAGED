<?php

class PageGestAggregate extends TagedPage
{
    const GAG_AGGREGATE = 'gag_aggregate';
    const GAG_PASSWORD = 'gag_password';
    const GAG_SUBMIT = 'gag_submit';
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Gestion Aggregations';
		$this->Password = 'EmrakulUnMouton';
		
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	protected function handle ( $Data )
	{
	    $Submit = Form::getData ( self::GAG_SUBMIT, '', $Data );

	    if ( $Submit != '' )
	    {
	        $this->Aggregate = Form::getData ( self::GAG_AGGREGATE, '', $Data );
	        $Password = Form::getData ( self::GAG_PASSWORD, '', $Data );
	        
            if ( ( $Password == $this->Password ) && ( NULL != $Aggregate ) ) 
	        {
	            Analysis::delete ( $this->Aggregate );
	        }
	    }
	    
	    $this->show ();
	}
	
	protected function show ( )
	{
	    $AggregateListObj = new AggregateList ();
	    $AggregateList = $AggregateListObj->getList ();
	    
        $TableContent = HTML::tr ( 
            HTML::th ( 'Aggregat' ) .
            HTML::th ( 'Actions' )
            );

        foreach ( $AggregateList as $Name => $Label )
        {
            $TableContent .= HTML::tr (
                HTML::td ( $Label ) .
                HTML::td ( 
                    HTML::link ( Menu::buildGenLink ( 'RunAnalysis', array ( PageRunAnalysis::RAN_AGGREGATE => $Name ) ), "Analyser" ) .
                    HTML::link ( Menu::buildGenLink ( 'ShowAggregate', array ( PageShowAggregate::SHOW_AGGREGATE => $Name ) ), "Afficher" ) .
                    HTML::link ( Menu::buildGenLink ( 'ChangeAggregate', array ( PageChangeAggregate::CHANGE_AGGREGATE => $Name ) ), "Modifier" ) .
                    HTML::link ( Menu::buildGenLink ( 'GestAggregate', array ( self::GAG_AGGREGATE => $Name ) ), "Supprimer" ) .
                    HTML::link ( Menu::buildGenLink ( 'ShowSkyCube', array ( PageShowSkyCube::SHOW_AGGREGATE => $Name ) ), "SkyCube" ) .
                    HTML::link ( Menu::buildGenLink ( 'RunSkyCubeAnalysis', array ( PageRunSkyCubeAnalysis::RAN_AGGREGATE => $Name ) ), "AnalyseSC" )
                    )
                );
        }
        
        $this->add ( HTML::link ( Menu::buildGenLink ( 'NewAggregate' ), "Ajouter" ) );
        $this->add ( HTML::div ( HTML::table ( $TableContent ) , array ( 'class' => 'taged_stats' ) ) ); 
	}

	protected $AggregateList;
} // PageChangeAggregate
