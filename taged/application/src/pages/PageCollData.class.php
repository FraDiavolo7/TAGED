<?php

class PageCollData extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Collection Data';
	
		$Switch = new Switcher ( $InputData );
		
		$Table = new CollTable ();
		
		$this->add ( HTML::div ( $Table->show (), array ( 'class' => 'coll_data' ) ) );
	}
} // PageCollParse
