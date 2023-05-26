<?php

class PageRunAnalysis2 extends TagedPage
{
    const RAN_AGGREGATE = 'ran_aggregate';
    const RAN_PASSWORD = 'ran_password';
    const RAN_ALGO = 'ran_algo';
    const RAN_SUBMIT = 'ran_submit';
    const SHOW_TEST = 'ran_test';
    
    const SHOW_INPUT = 'ran_input';
    const SHOW_ESPACE = 'ran_espace';
    const SHOW_ACCORDS = 'ran_accords';
    const SHOW_DATACUBE = 'ran_skycube';
    const SHOW_SKYCUBE = 'ran_sc_red';
    const SHOW_TAGED_CUBE = 'ran_sc_tag';
    
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

	protected function handle ( $Data )
	{
    	$Submit = Form::getData ( self::RAN_SUBMIT, '', $Data );
	    
    	$this->Aggregate = Form::getData ( self::RAN_AGGREGATE, '', $Data );
    	$this->Algo = Form::getData ( self::RAN_ALGO, '', $Data );
    	$this->ShowInput     = Form::getData ( self::SHOW_INPUT,      FALSE, $Data );
    	$this->ShowEspace    = Form::getData ( self::SHOW_ESPACE,     FALSE, $Data );
    	$this->ShowAccords   = Form::getData ( self::SHOW_ACCORDS,    FALSE, $Data );
    	$this->ShowDataCube  = Form::getData ( self::SHOW_DATACUBE,   FALSE, $Data );
    	$this->ShowSkyCube   = Form::getData ( self::SHOW_SKYCUBE,    FALSE, $Data );
    	$this->ShowTagedCube = Form::getData ( self::SHOW_TAGED_CUBE, FALSE, $Data );
    	$this->Test          = Form::getData ( self::SHOW_TEST,       FALSE, $Data );
    	$AggregateFile = NULL;
    	
    	if ( '' != $this->Aggregate )
    	{
    	    $this->AggregateObj = new AnalysisTest ( $this->Aggregate . '.ini', $this->Test );
    	}
    	
	    if ( $Submit != '' )
	    {
	        $Password = Form::getData ( self::RAN_PASSWORD, '', $Data );
	        
            if ( $Password == $this->Password ) 
	        {
	            $this->AggregateObj->setAlgorithm ( $this->Algo );
	            $this->AggregateObj->prepare ( );
	            $Result = $this->AggregateObj->compute ();
	            
	            if ( TRUE == $Result)
	            {
	                $this->Result = $this->AggregateObj->formatResult ();
	            }
	            else
	            {
	                $this->Result = $this->AggregateObj->getResult ();
	            }
	        }
	    }
	    
	    $this->show ();
	}
	
	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::RAN_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::RAN_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Algo = HTML::div ( HTML::select ( self::RAN_ALGO, $this->AlgoList, $this->Algo ), array ( 'class' => 'algo' ) );
	    $Submit = HTML::div ( HTML::submit( self::RAN_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    $CheckBoxes  = '';
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_TEST, 1, 'Test', $this->Test ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_INPUT, 1, 'Entr&eacute;e', $this->ShowInput ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ESPACE, 1, 'Espace Multidimensionnel', $this->ShowEspace ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ACCORDS, 1, 'Classes d&apos;accords', $this->ShowAccords ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_DATACUBE, 1, 'DataCube', $this->ShowDataCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_SKYCUBE, 1, 'SkyCube', $this->ShowSkyCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_TAGED_CUBE, 1, 'R&eacute;sultat Taged', $this->ShowTagedCube ), array ( 'class' => 'checkbox' ) );
	    
        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= HTML::div ( $CheckBoxes, array ( 'class' => 'checkboxes' ) );
            $Content .= $Algo;
            $Content .= $Submit;
        }
         
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	    
	    if ( '' != $this->Result )
	    {
	        $SkyCube = $this->AggregateObj->getSkyCube ();
    	    $Result = HTML::div ( $this->Result, array ( 'class' => 'result_msg' ) );
    	    
    	    if ( NULL != $SkyCube )
    	    {
    	        if ( $this->ShowInput     )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Entrée', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlInputData             ( $SkyCube ), array ( 'class' => 'result result_input' ) );
    	        }
    	        
    	        if ( $this->ShowEspace    )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Espace multidimensionel', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlMultidimensionalSpace ( $SkyCube ), array ( 'class' => 'result result_espace' ) );
    	        }
    	        
    	        if ( $this->ShowAccords   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'Classes d&apos;acccord', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS ), array ( 'class' => 'result result_accords' ) );
    	        }
    	        
    	        if ( $this->ShowDataCube  )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'DataCube', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_DATA_RAW ), array ( 'class' => 'result result_datacube' ) );
    	        }
    	        
    	        if ( $this->ShowSkyCube   )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'SkyCube', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_DATA_FILTERED ), array ( 'class' => 'result result_skycube' ) );
    	        }
    	        
    	        if ( $this->ShowTagedCube )
    	        {
    	            $Result .= HTML::div ( HTML::title ( 'TagedCube', 2 ), array ( 'class' => 'part_title' ) );
    	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_DATA_COMPUTED ), array ( 'class' => 'result result_taged_cube' ) );
    	        }
    	    }
    	    $this->add ( HTML::div ( $Result, array ( 'class' => 'results' ) ) );
	    }
	    
	}
	
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
	
	protected $Test;
	
	protected $ShowInput;
	protected $ShowEspace;
	protected $ShowAccords;
	protected $ShowDataCube;
	protected $ShowSkyCube;
	protected $ShowTagedCube;
} // PageRunAnalysis2
