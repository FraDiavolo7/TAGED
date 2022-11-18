<?php

class PageShowAggregate extends TagedPage
{
    const SHOW_AGGREGATE = 'change_aggregate';
    const SHOW_PASSWORD = 'change_password';
    const SHOW_SUBMIT = 'change_submit';
    
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

	protected function handle ( $Data )
	{
// 	    $this->add ( HTML::div ( print_r ( $Data, true ) ) );
	    
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

	protected $Result;
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
} // PageShowAggregate
