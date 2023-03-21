<?php

class PageShowSkyCube extends TagedPage
{
    const SHOW_AGGREGATE = 'sskc_aggregate';
    const SHOW_PASSWORD = 'sskc_password';
    const SHOW_SUBMIT = 'sskc_submit';
    
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
	            $this->SkyCube = $this->AggregateObj->getSkyCube ( TRUE, Cuboide::TO_MIN );
	        }
	    }
	    
	    $this->show ();
	}

	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::SHOW_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::SHOW_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Submit = HTML::div ( HTML::submit( self::SHOW_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    
	    $Result = '';
	    
	    if ( NULL != $this->SkyCube )
	    {
	        $Result = HTML::div ( SKDisplay::html ( $this->SkyCube ) );
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
	
	protected $SkyCube;
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
} // PageShowSkyCube
