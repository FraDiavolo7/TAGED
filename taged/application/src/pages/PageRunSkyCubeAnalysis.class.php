<?php

/**
 * @deprecated
 * @package Deprecated
 */
class PageRunSkyCubeAnalysis extends TagedPage
{
    const RAN_AGGREGATE = 'ran_aggregate';
    const RAN_PASSWORD = 'ran_password';
    const RAN_ALGO = 'ran_algo';
    const RAN_MIN1 = 'ran_min1';
    const RAN_MIN2 = 'ran_min2';
    const RAN_SUBMIT = 'ran_submit';
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Lancer Analyse SkyCube';
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
    	$this->Min1 = Form::getData ( self::RAN_MIN1, array (), $Data );
    	$this->Min2 = Form::getData ( self::RAN_MIN2, array (), $Data );
    	$this->Algo = Form::getData ( self::RAN_ALGO, '', $Data );
    	$AggregateFile = NULL;
    	
    	if ( '' != $this->Aggregate )
    	{
    	    $this->AggregateObj = new Analysis_old ( $this->Aggregate . '.ini' );
    	}
    	
	    if ( $Submit != '' )
	    {
	        $Password = Form::getData ( self::RAN_PASSWORD, '', $Data );
	        
            if ( $Password == $this->Password ) 
	        {
                $Result = $this->AggregateObj->runSkyCube ( $this->Algo, $this->Min1, $this->Min2, Cuboide::TO_MIN );
	            
// 	            if ( TRUE == $Result)
// 	            {
// 	                $this->Result = $this->AggregateObj->formatResult ();
// 	            }
// 	            else
// 	            {
// 	                $this->Result = $this->AggregateObj->getResult ();
// 	            }
	        }
	    }
	    
	    $this->show ();
	}
	
	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::RAN_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::RAN_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Algo = HTML::div ( HTML::select ( self::RAN_ALGO, $this->AlgoList, $this->Algo ), array ( 'class' => 'algo' ) );
	    
	    $Min = '';
	    
	    if ( NULL != $this->AggregateObj )
	    {
	        $Columns = $this->AggregateObj->getSkyCubeColumns ( );
    	    $MinTableHeader = HTML::th ( '' );
    	    $MinTableMin1   = HTML::th ( 'Min1' );
    	    $MinTableMin2   = HTML::th ( 'Min2' );
    	    foreach ( $Columns as $ColHeader => $Osef )
    	    {
                $MinTableHeader .=  HTML::td ( $ColHeader );
                $MinTableMin1   .=  HTML::td ( HTML::inputNum ( self::RAN_MIN1 . "[$ColHeader]", $this->Min1 [$ColHeader] ?? ANALYSIS_PARAM_M, 0, 1000  ) );
                $MinTableMin2   .=  HTML::td ( HTML::inputNum ( self::RAN_MIN2 . "[$ColHeader]", $this->Min2 [$ColHeader] ?? ANALYSIS_PARAM_N, 0, 1000  ) );
            }
	       
	        $Min = HTML::div ( 
	           HTML::table (
	               HTML::tr ( $MinTableHeader ) .
	               HTML::tr ( $MinTableMin1   ) .
	               HTML::tr ( $MinTableMin2   ) 
	               ) , array ( 'class' => 'min' ) );
	    }
	    
	    $Submit = HTML::div ( HTML::submit( self::RAN_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );

        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= $Algo;
            $Content .= $Min;
            $Content .= $Submit;
        }
         
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	    if ( '' != $this->Result )
	    {
	        $this->add ( $this->Result );
	    }
	    
	}
	
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
	
} // PageChangeAggregate
