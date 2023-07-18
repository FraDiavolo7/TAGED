<?php

class PageRunAnalysis extends TagedPage
{
    const RAN_AGGREGATE = 'ran_aggregate';
    const RAN_PASSWORD = 'ran_password';
    const RAN_ALGO = 'ran_algo';
    const RAN_SUBMIT = 'ran_submit';
    const SHOW_TEST = 'ran_test';
    const RAN_MIN = 'ran_min';
    const RAN_MAX = 'ran_max';
    
    const SHOW_INPUT = 'ran_input';
    const SHOW_ESPACE = 'ran_espace';
    const SHOW_ACCORDS = 'ran_accords';
    const SHOW_DATACUBE = 'ran_skycube';
    const SHOW_SKYCUBE = 'ran_sc_red';
    const SHOW_FUSION = 'ran_fusion';
    const SHOW_FUS_ABREGE = 'ran_fus_abr';
    const SHOW_COSKY = 'ran_cosky';
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
    	    $this->AggregateObj = new AnalysisTest ( $this->Aggregate . '.ini', $this->Test );
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
	            
// 	            if ( TRUE == $Result)
// 	            {
// 	                $this->Result = $this->AggregateObj->formatResult ();
// 	            }
// 	            else
// 	            {
 	                $this->Result = $this->AggregateObj->getResult ();
// 	            }
	        }
	    }
	    
	    $this->show ();
	}
	
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
	
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
	
	protected $Test;
	protected $Min;
	protected $Max;
	
	protected $CoSky1;
	protected $CoSky2;
	
	protected $ShowInput;
	protected $ShowEspace;
	protected $ShowAccords;
	protected $ShowDataCube;
	protected $ShowSkyCube;
	protected $ShowFusion;
	protected $ShowFusionAbregee;
	protected $ShowCoSky;
	protected $ShowTagedCube;
} // PageRunAnalysis
