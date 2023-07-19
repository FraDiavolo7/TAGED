<?php

/**
 *
 * @package TAGED\Pages
 */
class PageCollAggregate extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Collection Aggregate';
	
		$Switch = new Switcher ( $InputData );
		
		$Aggregate = new AggCollRequete ( FALSE );
		
		$this->add ( HTML::div ( $Aggregate->show (), array ( 'class' => 'coll_data' ) ) );
	}
} // PageCollAggregate
