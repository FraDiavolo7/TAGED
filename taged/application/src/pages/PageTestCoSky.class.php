<?php

/**
 * Test page
 * @package TAGED\Pages\Test
 */
class PageTestCoSky extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'TestCoSky';
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}
	
	protected function handle ( $Data )
	{
	    $CoSky = new CoSky ( NULL );
	    
	    $CoSky->run ();
	    
	    $Data = $CoSky->getData ();
	    
	    $ToShow = array ();
	    
	    foreach ( $Data as $Attr => $Entries )
	    {
	        foreach ( $Entries as $Tuple => $Value )
	        {
	            $ToShow [$Tuple] [$Attr] = $Value;
	        }
	    }
	    
	    foreach ( $ToShow as $Tuple => $Row )
	    {
	        $ToShow [$Tuple] ['Score'] = $CoSky->getScore ($Tuple);
	    }

	    $this->add ( HTML::tableFull ( $ToShow, array ( 'class' => 'cuboide'), TRUE ) );
	}
	
} // PageTestCoSky
