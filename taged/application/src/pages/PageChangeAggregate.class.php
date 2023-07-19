<?php

/**
 *
 * @package TAGED\Pages
 */
class PageChangeAggregate extends TagedPage
{
    const CHANGE_AGGREGATE = 'change_aggregate';
    const CHANGE_PASSWORD = 'change_password';
    const CHANGE_REQUEST = 'change_request';
    const CHANGE_REL_COLS  = 'change_relation_cols';
    const CHANGE_MES_COLS  = 'change_measure_cols';
    const CHANGE_SUBMIT = 'change_submit';
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Modifier Aggregation';
		$this->Request = '';
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
// 	    $this->add ( HTML::div ( print_r ( $Data, true ) ) );
	    
	    $Submit = Form::getData ( self::CHANGE_SUBMIT, '', $Data );
        $this->Aggregate = Form::getData ( self::CHANGE_AGGREGATE, '', $Data );
        $AggregateFile = NULL;
        
        if ( '' != $this->Aggregate )
        {
            $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini' );
            $AggregateFile = $this->AggregateObj->getAggregateFile ( FALSE );
            $this->Request = $AggregateFile->getRequest ();
        }
	    if ( $Submit != '' )
	    {
	        $this->Request = Form::getData ( self::CHANGE_REQUEST, '', $Data );
	        $RelCols = Form::getData ( self::CHANGE_REL_COLS, '', $Data );
	        $MesCols = Form::getData ( self::CHANGE_MES_COLS, '', $Data );
	        $Password = Form::getData ( self::CHANGE_PASSWORD, '', $Data );
	        
	        if ( ( $Password == $this->Password ) && ( NULL != $AggregateFile ) ) 
	        {
	            $this->AggregateObj->setRelationCols ( $RelCols );
	            $this->AggregateObj->setMeasureCols ( $MesCols );
	            $this->AggregateObj->write ( );
	            $AggregateFile->setRequest ( $this->Request );
	            echo "Updating Request for " . $this->Aggregate;
	        }
	    }
	    
	    $this->show ();
	}
	
	protected function show ( )
	{
	    if ( '' == $this->Request ) $this->Request = ' ' ;
	    $Password = HTML::div ( HTML::inputPassword ( self::CHANGE_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::CHANGE_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    
	    $Request = '';
	    $RelCols = '';
	    $MesCols = '';
	    
	    if ( NULL != $this->AggregateObj )
	    {
	        $RelCols = HTML::div ( HTML::inputText ( self::CHANGE_REL_COLS, $this->AggregateObj->getRelationCols (), 'Attributs de la Relation' ), array ( 'class' => 'aggregate' ) );
	        $MesCols = HTML::div ( HTML::inputText ( self::CHANGE_MES_COLS, $this->AggregateObj->getMeasureCols (), 'Attributs de Mesure' ), array ( 'class' => 'aggregate' ) );
	        $Request = HTML::div ( HTML::textarea  ( self::CHANGE_REQUEST,  $this->Request, 35, 160 ), array ( 'class' => 'request' ) );
	    }
	        
	    $Submit = HTML::div ( HTML::submit( self::CHANGE_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );

        $Content = $Aggregate;

        if ( '' != $this->Aggregate )
        {
            $Content .= $Password;
            $Content .= $RelCols;
            $Content .= $MesCols;
            $Content .= $Request;
            $Content .= $Submit;
        }

	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}

	protected $Request;
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
} // PageChangeAggregate
