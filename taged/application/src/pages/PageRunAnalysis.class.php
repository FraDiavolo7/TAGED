<?php

class PageRunAnalysis extends TagedPage
{
    const RAN_AGGREGATE = 'ran_aggregate';
    const RAN_PASSWORD = 'ran_password';
    const RAN_SUBMIT = 'ran_submit';
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Lancer Analyse';
		$this->Result = '';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	protected function handle ( $Data )
	{
    	$Submit = Form::getData ( self::RAN_SUBMIT, '', $Data );
	    
    	$this->Aggregate = Form::getData ( self::RAN_AGGREGATE, '', $Data );
    	$AggregateFile = NULL;
    	
    	if ( '' != $this->Aggregate )
    	{
    	    $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini' );
    	}
    	
	    if ( $Submit != '' )
	    {
	        $this->Aggregate = Form::getData ( self::RAN_AGGREGATE, '', $Data );
	        $Password = Form::getData ( self::RAN_PASSWORD, '', $Data );
	        
            if ( $Password == $this->Password ) 
	        {
	            $this->Result = $this->AggregateObj->run ( ANALYSIS_ALGO, ANALYSIS_PARAM_M, ANALYSIS_PARAM_N );
	        }
	    }
	    
	    $this->show ();
	}
	
	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::RAN_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::RAN_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Submit = HTML::div ( HTML::submit( self::RAN_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );

        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= $Submit;
        }
         
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	    if ( '' != $this->Result )
	    {
	        $this->add ( HTML::pre ( $this->Result ) );
	    }
	    
	}
	
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
	
} // PageChangeAggregate
