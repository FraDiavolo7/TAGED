<?php

class PageShowSkyCube extends TagedPage
{
    const SHOW_AGGREGATE = 'sskc_aggregate';
    const SHOW_PASSWORD = 'sskc_password';
    const SHOW_SUBMIT = 'sskc_submit';

    const SHOW_INPUT = 'sskc_input';
    const SHOW_ESPACE = 'sskc_espace';
    const SHOW_ACCORDS = 'sskc_accords';
    const SHOW_DATACUBE = 'sskc_skycube';
    const SHOW_SKYCUBE = 'sskc_sc_red';
    const SHOW_TAGED_CUBE = 'sskc_sc_tag';
    
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Afficher Aggregation en SkyCube Emergent';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		$this->SkyCube = NULL;
		
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data ); 
	} 
	
	protected function handle ( $Data )
	{
	    $Submit = Form::getData ( self::SHOW_SUBMIT, '', $Data );
	    $this->ShowInput     = Form::getData ( self::SHOW_INPUT,      FALSE, $Data );
	    $this->ShowEspace    = Form::getData ( self::SHOW_ESPACE,     FALSE, $Data );
	    $this->ShowAccords   = Form::getData ( self::SHOW_ACCORDS,    FALSE, $Data );
	    $this->ShowDataCube  = Form::getData ( self::SHOW_DATACUBE,   FALSE, $Data );
	    $this->ShowSkyCube   = Form::getData ( self::SHOW_SKYCUBE,    FALSE, $Data );
	    $this->ShowTagedCube = Form::getData ( self::SHOW_TAGED_CUBE, FALSE, $Data );
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
	            $this->SkyCube = $this->AggregateObj->getSkyCube ( FALSE, Cuboide::TO_MIN );
	        }
	    }
	    
	    $this->show ();
	}

	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::SHOW_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $CheckBoxes = '';
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_INPUT, 1, 'Entr&eacute;e', $this->ShowInput ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ESPACE, 1, 'Espace Multidimensionnel', $this->ShowEspace ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_ACCORDS, 1, 'Classes d&apos;accords', $this->ShowAccords ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_DATACUBE, 1, 'DataCube', $this->ShowDataCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_SKYCUBE, 1, 'SkyCube', $this->ShowSkyCube ), array ( 'class' => 'checkbox' ) );
	    $CheckBoxes .= HTML::div ( HTML::checkbox ( self::SHOW_TAGED_CUBE, 1, 'R&eacute;sultat Taged', $this->ShowTagedCube ), array ( 'class' => 'checkbox' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::SHOW_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Submit = HTML::div ( HTML::submit( self::SHOW_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    
	    $Result = '';
	    
	    if ( NULL != $this->SkyCube )
	    {
	        if ( $this->ShowInput     ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'Entrée', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlInputData             ( $this->SkyCube ), array ( 'class' => 'result result_input' ) );
	        }
	        
	        if ( $this->ShowEspace    ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'Espace multidimensionel', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlMultidimensionalSpace ( $this->SkyCube ), array ( 'class' => 'result result_espace' ) );
	        }
	        
	        if ( $this->ShowAccords   ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'Classes d&apos;acccord', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $this->SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS ), array ( 'class' => 'result result_accords' ) );
	        }
	        
	        if ( $this->ShowDataCube  ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'DataCube', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $this->SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS | SKDisplay::SHOW_DATA_RAW ), array ( 'class' => 'result result_datacube' ) );
	        }
	        
	        if ( $this->ShowSkyCube   ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'SkyCube', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $this->SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS | SKDisplay::SHOW_DATA_FILTERED ), array ( 'class' => 'result result_skycube' ) );
	        }
	        
	        if ( $this->ShowTagedCube ) 
	        {
	            $Result .= HTML::div ( HTML::title ( 'TagedCube', 2 ), array ( 'class' => 'part_title' ) );
	            $Result .= HTML::div ( SKDisplay::htmlSkyCubeParam ( $this->SkyCube, SKDisplay::SHOW_FILTERED | SKDisplay::SHOW_REMOVED | SKDisplay::SHOW_EQUIV_CLASS | SKDisplay::SHOW_DATA_COMPUTED ), array ( 'class' => 'result result_taged_cube' ) );
	        }
	    }
	    
	    $Content = $Aggregate;
	    
	    if ( '' != $this->Aggregate )
	    {
	        $Content .= $Password;
	        $Content .= HTML::div ( $CheckBoxes, array ( 'class' => 'checkboxes' ) );
	        $Content .= $Submit;
	        $Content .= HTML::div ( $Result, array ( 'class' => 'results' ) );
	    }
	    
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}
	
	protected $SkyCube;
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $ShowInput;
	protected $ShowEspace;
	protected $ShowAccords;
	protected $ShowDataCube;
	protected $ShowSkyCube;
	protected $ShowTagedCube;
	
	protected $AggregateListObj;
	protected $AggregateList;
} // PageShowSkyCube
